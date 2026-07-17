/* ============================================================
   NEXORA — socialnet.js (nettoyé)
   La colonne "Discussions" (droite) est maintenant gérée par le
   composant Livewire chat-box.blade.php, plus par ce fichier.
   Tout le code lié à #chatList / #dmListView / #addFriendsView /
   #translateBtn / contacts / requests / suggestions a été retiré
   car ces éléments n'existent plus dans le DOM (ils causaient un
   crash JS qui bloquait tout le reste du script).
   ============================================================ */

/* ---------- STORIES ---------- */
/* La barre de stories, la création et le visionnage sont maintenant gérés
   côté serveur par le composant Livewire (socialnet.blade.php + les
   modèles Story / StoryView). Ce fichier ne touche plus à #stories. */

/* ---------- SALONS ---------- */

/* La liste des salons, l'ouverture d'un salon et l'envoi de messages sont
   maintenant gérés côté serveur par le composant Livewire (socialnet.blade.php
   + les modèles Room / RoomMessage). Ce fichier JS ne fait plus que gérer
   l'affichage (bascule Flux <-> Salons en mobile, likes, dropdowns du header). */


/* ---------- LIKES ---------- */
/* Les likes sont maintenant gérés côté serveur par Livewire
   (wire:click="toggleLike" dans socialnet.blade.php + les modèles
   Post / PostLike). Ce fichier ne simule plus rien en local pour
   éviter un désync avec l'état réel en base de données. */

/* ---------- DROPDOWNS HEADER ---------- */
function toggleDrop(id, e) {
  e.stopPropagation();
  document.querySelectorAll('.dropdown').forEach(d => {
    if (d.id !== id) d.classList.add('hidden-x');
  });
  document.getElementById(id).classList.toggle('hidden-x');
}
document.getElementById('btnNotif').addEventListener('click', e => toggleDrop('dropdownNotif', e));
document.getElementById('btnProfile').addEventListener('click', e => toggleDrop('dropdownProfile', e));
document.addEventListener('click', () => {
  document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden-x'));
});
function goToProfile() {
  document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden-x'));
  window.location.href = "/profile";
}

/* ---------- ONGLETS MOBILE ---------- */
document.querySelectorAll('.tab-mobile').forEach(t => {
  t.addEventListener('click', () => {
    document.querySelectorAll('.tab-mobile').forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    const target = t.dataset.tab;
    ['left', 'center', 'right'].forEach(k => {
      const el = document.getElementById('col' + k[0].toUpperCase() + k.slice(1));
      if (k === target) { el.classList.add('mobile-visible'); } else { el.classList.remove('mobile-visible'); }
    });
  });
});

/* ---------- SWIPE MOBILE ---------- */
let touchStartX = 0;
const body = document.getElementById('appBody');
body.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
body.addEventListener('touchend', e => {
  const dx = e.changedTouches[0].clientX - touchStartX;
  if (Math.abs(dx) < 70) return;
  const tabs = ['left', 'center', 'right'];
  const current = document.querySelector('.tab-mobile.active')?.dataset.tab;
  const idx = tabs.indexOf(current);
  const next = dx < 0 ? Math.min(2, idx + 1) : Math.max(0, idx - 1);
  if (next !== idx) document.querySelector(`.tab-mobile[data-tab="${tabs[next]}"]`).click();
}, { passive: true });

/* ---------- REAL WEBRTC VOICE CALLS & SIGNALING ---------- */
let localStream = null;
let peerConnection = null;
let callTimer = null;
let callDuration = 0;
let myUserId = null;
let pendingOffer = null;
let pendingIceCandidates = [];
let isMuted = false;

// Track active call details
let activeCall = {
  active: false,
  type: null, // 'chat' or 'room'
  targetId: null, // connectionId or roomId
  role: null, // 'caller' or 'callee'
  peerId: null
};

// Configuration STUN Google & TURN (dynamique ou fallback)
// En production, il est indispensable de configurer un serveur TURN (via les variables .env :
// TURN_SERVER_URL, TURN_SERVER_USERNAME et TURN_SERVER_CREDENTIAL)
// pour traverser les pare-feux et NAT restrictifs (ex: coturn auto-hébergé, Metered.ca, Twilio, ou Cloudflare Calls).
const rtcConfig = window.rtcConfig || {
  iceServers: [
    { urls: 'stun:stun.l.google.com:19302' }
  ]
};

// HTML audio element for playing remote stream
let remoteAudio = document.createElement('audio');
remoteAudio.autoplay = true;
document.body.appendChild(remoteAudio);

function initIncomingCallListener() {
  if (window.chatBoxLivewire) {
    myUserId = window.chatBoxLivewire.get('myUserId');
  } else if (window.socialnetLivewire) {
    myUserId = window.socialnetLivewire.get('myUserId');
  }

  if (!myUserId) {
    setTimeout(initIncomingCallListener, 1000);
    return;
  }

  // Subscribe to personal channel for receiving direct calls
  window.Echo.private('App.Models.User.' + myUserId)
    .listen('.CallIncoming', (e) => {
      if (activeCall.active) {
        return;
      }
      showIncomingCallModal(e.senderName, e.senderId, e.type, e.targetId);
    });
}

document.addEventListener('livewire:init', initIncomingCallListener);
if (window.Livewire) {
  initIncomingCallListener();
}

// Active Echo Listeners for signaling
let activeSignalingChannel = null;

function subscribeToSignalingChannel(type, targetId) {
  if (activeSignalingChannel) {
    activeSignalingChannel.stopListening('.CallOffer');
    activeSignalingChannel.stopListening('.CallAnswer');
    activeSignalingChannel.stopListening('.IceCandidate');
    activeSignalingChannel.stopListening('.CallEnded');
    activeSignalingChannel.stopListening('.CallIncoming');
  }

  const channelName = type === 'chat' ? 'chat.' + targetId : 'room.' + targetId;
  activeSignalingChannel = window.Echo.private(channelName);

  activeSignalingChannel.listen('.CallOffer', async (e) => {
    if (e.senderId === myUserId) return;
    await handleCallOffer(e.offer, e.senderId);
  });

  activeSignalingChannel.listen('.CallAnswer', async (e) => {
    if (e.senderId === myUserId) return;
    await handleCallAnswer(e.answer);
  });

  activeSignalingChannel.listen('.IceCandidate', async (e) => {
    if (e.senderId === myUserId) return;
    await handleIceCandidate(e.candidate);
  });

  activeSignalingChannel.listen('.CallEnded', (e) => {
    if (e.senderId === myUserId) return;
    handleCallEndedExternally();
  });

  // For group rooms, listen to incoming calls inside the room
  if (type === 'room') {
    activeSignalingChannel.listen('.CallIncoming', (e) => {
      if (e.senderId === myUserId) return;
      if (!activeCall.active) {
        showIncomingCallModal(e.senderName, e.senderId, 'room', targetId);
      }
    });
  }
}

document.addEventListener('discussion-opened', (e) => {
  subscribeToSignalingChannel('chat', e.detail.connectionId);
});

document.addEventListener('room-opened', (e) => {
  subscribeToSignalingChannel('room', e.detail.roomId);
});

window.startCall = async function (username, type = 'chat', targetId = null) {
  if (!targetId) {
    if (type === 'chat' && window.chatBoxLivewire) {
      targetId = window.chatBoxLivewire.get('connectionId');
    } else if (type === 'room' && window.socialnetLivewire) {
      targetId = window.socialnetLivewire.get('activeRoomId');
    }
  }

  if (!targetId) return;

  activeCall = {
    active: true,
    type: type,
    targetId: targetId,
    role: 'caller',
    peerId: null
  };

  // Ensure JS is subscribed to signaling
  subscribeToSignalingChannel(type, targetId);

  showCallingUI(username);

  try {
    localStream = await navigator.mediaDevices.getUserMedia({ audio: true });

    createPeerConnection();

    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);

    if (type === 'chat') {
      window.chatBoxLivewire.call('broadcastCallIncoming');
      setTimeout(() => {
        if (activeCall.active) {
          window.chatBoxLivewire.call('broadcastCallOffer', offer);
        }
      }, 800);
    } else {
      window.socialnetLivewire.call('broadcastRoomCallIncoming');
      setTimeout(() => {
        if (activeCall.active) {
          window.socialnetLivewire.call('broadcastRoomCallOffer', offer);
        }
      }, 800);
    }

    updateCallStatus("Sonnerie...");
  } catch (err) {
    console.error('Error starting WebRTC call:', err);
    updateCallStatus("Erreur micro : " + err.message);
    setTimeout(window.endCall, 3000);
  }
};

function showCallingUI(username) {
  const modal = document.getElementById('callModal');
  if (!modal) return;

  document.getElementById('callName').innerText = '@' + username;
  document.getElementById('callAvatar').querySelector('div').innerText = username.substring(0, 2).toUpperCase();

  const statusEl = document.getElementById('callStatus');
  statusEl.innerText = "Appel en cours...";
  statusEl.className = "text-sm text-neutral-500 mt-1 animate-pulse";

  const actionsEl = document.getElementById('callActions');
  actionsEl.innerHTML = `
    <button onclick="endCall()" class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl" style="background:linear-gradient(135deg,#c02b3a,#7d1a24);box-shadow:0 10px 30px -10px rgba(192,43,58,.6)" title="Annuler">✕</button>
  `;

  modal.classList.remove('hidden-x');
  modal.classList.add('flex');
}

function showIncomingCallModal(senderName, senderId, type, targetId) {
  activeCall = {
    active: true,
    type: type,
    targetId: targetId,
    role: 'callee',
    peerId: senderId
  };

  subscribeToSignalingChannel(type, targetId);

  const modal = document.getElementById('callModal');
  if (!modal) return;

  document.getElementById('callName').innerText = '@' + senderName;
  document.getElementById('callAvatar').querySelector('div').innerText = senderName.substring(0, 2).toUpperCase();

  const statusEl = document.getElementById('callStatus');
  statusEl.innerText = "Appel entrant...";
  statusEl.className = "text-sm text-neutral-500 mt-1 animate-pulse";

  const actionsEl = document.getElementById('callActions');
  actionsEl.innerHTML = `
    <button onclick="acceptCall()" class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl" style="background:linear-gradient(135deg,#22c55e,#15803d);box-shadow:0 10px 30px -10px rgba(34,197,94,.6)" title="Accepter">📞</button>
    <button onclick="endCall()" class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl" style="background:linear-gradient(135deg,#c02b3a,#7d1a24);box-shadow:0 10px 30px -10px rgba(192,43,58,.6)" title="Refuser">✕</button>
  `;

  modal.classList.remove('hidden-x');
  modal.classList.add('flex');
}

window.acceptCall = async function () {
  updateCallStatus("Connexion...");

  const actionsEl = document.getElementById('callActions');
  actionsEl.innerHTML = `
    <button onclick="toggleMute()" id="btnMute" class="w-14 h-14 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Muet">🎤</button>
    <button onclick="endCall()" class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl" style="background:linear-gradient(135deg,#c02b3a,#7d1a24);box-shadow:0 10px 30px -10px rgba(192,43,58,.6)" title="Raccrocher">✕</button>
  `;

  try {
    localStream = await navigator.mediaDevices.getUserMedia({ audio: true });

    createPeerConnection();

    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    if (pendingOffer) {
      await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));

      // Apply queued ICE candidates
      for (const candidate of pendingIceCandidates) {
        try {
          await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (e) {
          console.error("Error adding queued ice candidate:", e);
        }
      }
      pendingIceCandidates = [];

      const answer = await peerConnection.createAnswer();
      await peerConnection.setLocalDescription(answer);

      if (activeCall.type === 'chat') {
        window.chatBoxLivewire.call('broadcastCallAnswer', answer);
      } else {
        window.socialnetLivewire.call('broadcastRoomCallAnswer', answer);
      }

      pendingOffer = null;
      startCallTimer();
    }
  } catch (err) {
    console.error('Error accepting call:', err);
    updateCallStatus("Erreur micro : " + err.message);
    setTimeout(window.endCall, 3000);
  }
};

async function handleCallOffer(offer, senderId) {
  pendingOffer = offer;
}

async function handleCallAnswer(answer) {
  if (peerConnection && activeCall.role === 'caller') {
    await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));

    // Apply queued ICE candidates
    for (const candidate of pendingIceCandidates) {
      try {
        await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
      } catch (e) {
        console.error("Error adding queued ice candidate:", e);
      }
    }
    pendingIceCandidates = [];

    startCallTimer();
  }
}

async function handleIceCandidate(candidate) {
  if (peerConnection && peerConnection.remoteDescription) {
    try {
      await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
    } catch (e) {
      console.error("Error adding ice candidate:", e);
    }
  } else {
    pendingIceCandidates.push(candidate);
  }
}

function createPeerConnection() {
  if (peerConnection) {
    peerConnection.close();
  }

  peerConnection = new RTCPeerConnection(rtcConfig);

  peerConnection.onicecandidate = (event) => {
    if (event.candidate && activeCall.active) {
      if (activeCall.type === 'chat') {
        window.chatBoxLivewire.call('broadcastIceCandidate', event.candidate);
      } else {
        window.socialnetLivewire.call('broadcastRoomIceCandidate', event.candidate);
      }
    }
  };

  peerConnection.ontrack = (event) => {
    if (event.streams && event.streams[0]) {
      remoteAudio.srcObject = event.streams[0];
      updateCallStatus("Appel établi 🟢");
    }
  };
}

window.endCall = function () {
  if (activeCall.active) {
    if (activeCall.type === 'chat' && window.chatBoxLivewire) {
      window.chatBoxLivewire.call('broadcastCallEnded');
    } else if (activeCall.type === 'room' && window.socialnetLivewire) {
      window.socialnetLivewire.call('broadcastRoomCallEnded');
    }
  }

  cleanupCall();
};

function handleCallEndedExternally() {
  cleanupCall();
}

function cleanupCall() {
  activeCall.active = false;
  activeCall.type = null;
  activeCall.targetId = null;
  activeCall.role = null;
  pendingOffer = null;
  isMuted = false;

  if (callTimer) {
    clearInterval(callTimer);
    callTimer = null;
  }

  if (peerConnection) {
    peerConnection.close();
    peerConnection = null;
  }

  if (localStream) {
    localStream.getTracks().forEach(track => track.stop());
    localStream = null;
  }

  remoteAudio.srcObject = null;

  const modal = document.getElementById('callModal');
  if (modal) {
    modal.classList.add('hidden-x');
    modal.classList.remove('flex');
  }
}

window.toggleMute = function () {
  if (localStream) {
    isMuted = !isMuted;
    localStream.getAudioTracks().forEach(track => track.enabled = !isMuted);
    const btn = document.getElementById('btnMute');
    if (btn) {
      btn.innerText = isMuted ? "🔇" : "🎤";
      btn.classList.toggle('bg-[#6b1f2a]/10', isMuted);
    }
  }
};

function startCallTimer() {
  callDuration = 0;
  const statusEl = document.getElementById('callStatus');
  statusEl.className = "text-sm text-green-600 font-semibold mt-1";

  if (callTimer) clearInterval(callTimer);
  callTimer = setInterval(() => {
    callDuration++;
    const mins = String(Math.floor(callDuration / 60)).padStart(2, '0');
    const secs = String(callDuration % 60).padStart(2, '0');
    statusEl.innerText = `Appel établi : ${mins}:${secs} 🟢`;
  }, 1000);
}

function updateCallStatus(text) {
  const statusEl = document.getElementById('callStatus');
  if (statusEl) {
    statusEl.innerText = text;
  }
}
