const streams = new Map();

function formatCallTime(elapsed) {
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;

    if (hours > 0) {
        return (
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    } else {
        return (
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    }
}

async function init_Call(wire, sendingCall, incomingCall, Call, settings, peerSettings) {
    const already_ended = ['cancelled', 'declined', 'ended', 'stopped'].includes(Call.status);
    if(!already_ended) {
        const callDiv = document.querySelector('#call');
        if (!callDiv){
            notifyConversation('The call element isn\'t detected.');
            return;
        }
        if(!['voice', 'video'].includes(Call?.type)){
            notifyConversation('Unknown call type');
            return;
        }






        // Stream for sending call
        let stream = null;
        if(!(callDiv.stream instanceof MediaStream) && (sendingCall || (incomingCall && Call?.status === 'accepted'))){
            stream = await startStream(Call?.type);
            if(stream instanceof MediaStream){
                callDiv.stream = stream;
                streams.set(stream.id, stream);
                settings.stream = stream.id;
                wire?.setStream(stream.id);

                const {mic: micStream, video: videoStream} = destructStream(stream);
                if(Call?.type === 'voice'){

                } else if(Call?.type === 'video'){
                    const videoElement = callDiv?.querySelector('video[x-ref="local"]');
                    attachVideoStream(videoStream, videoElement);
                }
            } else if(stream === null){
                if(sendingCall){
                    wire?.cancelDeclineEndCall();
                } else if(incomingCall){
                    wire?.stopCall(); //
                }
            }

            // When incoming call
            if(Call?.status === 'accepted'){
                start_webrtc_connection(wire, Call, peerSettings)
            }
        }

        // When sending call
        if(callDiv?.sendingCallStream !== true && sendingCall && Call?.status === 'accepted'){
            callDiv.sendingCallStream = true;
            start_webrtc_connection(wire, Call, peerSettings)
        }

        // Visualize the stream
        if(callDiv?.stream instanceof MediaStream){
            const video_feed = callDiv?.querySelector('#video-feed');
            if(Call?.type === 'video' && video_feed){
                const video_local_div = video_feed?.querySelector('video[x-ref="local"]')?.closest('div');
                if(video_local_div && ['pending', 'accepted'].includes(Call?.status)){
                    if(Call.status === 'pending'){
                        Object.assign(video_local_div.style, { top:'0', right:'0', width:'100%', height:'100%' });
                    } else if(Call.status === 'accepted'){
                        video_local_div.removeAttribute('style');
                    }
                }
            }
        }




        // Control the call interval
        if(callDiv.preventTimer !== true && callDiv.callInterval){
            clearInterval(callDiv.callInterval);
            delete callDiv.callInterval;

            if(Call?.status === 'accepted'){
                callDiv.preventTimer = true;
            }
        }


        const startTime = new Date();
        if(!callDiv.callInterval){
            callDiv.callInterval = setInterval(async () => {
                const span = callDiv.querySelector('#call-text');
                const callExist = document.querySelector('#call');
                if(!span || !callExist){
                    clearInterval(callDiv.callInterval);
                    delete callDiv.callInterval;
                    return;
                }

                const elapsed = Math.floor((new Date() - startTime) / 1000);

                if(Call?.status === 'pending'){
                    if (elapsed >= settings.ringTime) {
                        if(sendingCall){
                            clearInterval(callDiv.callInterval);
                            delete callDiv.callInterval;
                            wire.cancelDeclineEndCall();
                        } else {
                            // Disable action buttons
                        }
                    } else if(peerSettings?.ringing === false){
                        // Re-try call if recipient not connected
                        if(elapsed % 3 === 0){
                            wire?.broadcastCall(true, {skipBusy: true});
                        }
                    }
                } else if (Call?.status === 'accepted') {
                    const time = callDiv.querySelector('#call-text > time');
                    time?.setAttribute('data-text', formatCallTime(elapsed));

                    if(callDiv.pingInterval){
                        clearInterval(callDiv.pingInterval);
                        delete callDiv.pingInterval;
                    }
                    if(elapsed % 3 === 0){
                        wire?.pingCall();
                    }
                }
            }, 1000);



            let p = 1;
            callDiv.pingInterval = setInterval(() => {
                const ping = callDiv.querySelector('#call-text .ping');
                if (ping) ping.innerText = ['', '.', '..', '...'][p % 4];
                p++;
            }, 400)
        }
    }
}









































// --------------- START - Video Call Drag
function setVideoFeedPosition(el){
    const rect = el.getBoundingClientRect();
    const left = (rect.width/2) + 'px';
    el.style.left = `calc(50% - ${left})`;
    el.style.top = '5px';
    el.style.position = 'fixed';
    el.style.transform = '';
}


document.addEventListener('mousedown', (event) => {
    const target = event?.target;
    const videoFeed = target?.id === 'video-feed' ? target : target?.closest('#video-feed');
    if (!videoFeed) return;

    event.preventDefault();
    videoFeed.classList.add('dragging');

    // Calculate offset of mouse inside element
    const rect = videoFeed.getBoundingClientRect();
    const offsetX = event.clientX - rect.left;
    const offsetY = event.clientY - rect.top;

    videoFeed.style.position = 'fixed';

    function onMouseMove(e) {
        videoFeed.style.left = e.clientX - offsetX + 'px';
        videoFeed.style.top = e.clientY - offsetY + 'px';
    }

    function onMouseUp() {
        videoFeed.classList.remove('dragging');
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
});
// --------------- END - Video Call Drag




















export async function checkMicCameraPermission() {
    const mic = await navigator.permissions.query({ name: 'microphone' });
    const camera = await navigator.permissions.query({ name: 'camera' });

    return {
        mic: mic.state === 'granted',
        camera: camera.state === 'granted'
    };
}


function notifyConversation(msg){
    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('refresh-message-alert', {
            response: { error: msg },
            end_preference: { html: true }
        });
    } else {
        alert(msg);
    }
}


export async function requestForMicCameraPermission(type = null, combine = false) {
    try {
        let constraints = {};

        if (type === "mic") constraints.audio = true;
        else if (type === "camera") constraints.video = { facingMode: "user" };
        else constraints = { audio: true, video: { facingMode: "user" } }; // request both if type not specified

        const stream = await navigator.mediaDevices.getUserMedia(constraints);

        const micStream = type === "camera" ? null : new MediaStream(stream.getAudioTracks());
        const cameraStream = type === "mic" ? null : new MediaStream(stream.getVideoTracks());

        if (type === "mic") return micStream;
        if (type === "camera") return cameraStream;

        return combine ?  stream : { mic: micStream, camera: cameraStream };
    } catch (error) {
        console.warn("Permission: " + error);
        if (type === "mic")     notifyConversation('<i class="fas fa-microphone"></i> Microphone access denied or unavailable.');
        if (type === "camera")  notifyConversation('<i class="fas fa-video"></i> Camera access denied or unavailable.');
        if (type === null)      notifyConversation('<i class="fas fa-microphone"></i> Microphone or <i class="fas fa-video"></i> Camera access denied or unavailable.');

        if (type === "mic") return null;
        if (type === "camera") return null;
        return { mic: null, camera: null };
    }
}


export function validateMicStream(stream) {
    return stream instanceof MediaStream && stream.getAudioTracks().length && stream.getAudioTracks()[0].readyState === "live";
}
export function validateCameraStream(stream) {
    return stream instanceof MediaStream && stream.getVideoTracks().length && stream.getVideoTracks()[0].readyState === "live";
}
export function validateStreams(micStream, camStream, combine = false) {
    const mic = validateMicStream(micStream);
    const camera = validateCameraStream(camStream);
    return combine ? (mic && camera) : { mic, camera };
}

export const validateStream = (combinedStream, combine = false) => {
    if (!(combinedStream instanceof MediaStream)) return combine ? false : { mic: false, camera: false };
    const mic = validateMicStream(new MediaStream(combinedStream.getAudioTracks()));
    const camera = validateCameraStream(new MediaStream(combinedStream.getVideoTracks()));
    return combine ? (mic && camera) : { mic, camera };
};





export async function startMicStream() {
    try {
        return await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
    } catch (error) {
        console.warn("Permission: " + error);
        notifyConversation('<i class="fas fa-microphone"></i> Microphone access denied or unavailable.');
        return null;
    }
}

export async function startVideoStream() {
    try {
        return await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
    } catch (error) {
        console.warn("Permission: " + error);
        notifyConversation('<i class="fas fa-video"></i> Camera access denied or unavailable.');
        return null;
    }
}

export async function startMicCameraStream() {
    try {
        return await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: true });
    } catch (error) {
        console.warn("Permission: " + error);
        notifyConversation('<i class="fas fa-microphone"></i> Microphone or <i class="fas fa-video"></i> Camera access denied or unavailable.');
        return null;
    }
}

export async function startStream(type = null) {
    if (type === "voice") {
        return await startMicStream();
    } else if (type === "video") {
        // return await startVideoStream();
        return await startMicCameraStream();
    } else {
        return await startMicCameraStream();
    }
}


export function stopStream(stream){
    if(typeof stream === 'string'){
        const stream_id = stream;
        stream = streams.get(stream_id);
        streams.delete(stream_id);
    }
    if(!(stream instanceof MediaStream)) return false;
    stream.getTracks().forEach(track => track.stop());
    return true;
}

export function stopMicStream(micStream) {
    if (!(micStream instanceof MediaStream)) return false;
    micStream.getAudioTracks().forEach(track => track.stop());
    return true;
}

export function stopCameraStream(cameraStream) {
    if (!(cameraStream instanceof MediaStream)) return false;
    cameraStream.getVideoTracks().forEach(track => track.stop());
    return true;
}

export function destructStream(stream) {
    if (!(stream instanceof MediaStream)) {
        throw new Error("Invalid argument: stream must be an instance of MediaStream");
        return;
    }

    let micStream = null;
    let videoStream = null;

    const audioTracks = stream.getAudioTracks();
    const videoTracks = stream.getVideoTracks();

    if (audioTracks.length > 0) {
        micStream = new MediaStream(audioTracks);
    }

    if (videoTracks.length > 0) {
        videoStream = new MediaStream(videoTracks);
    }

    return { mic: micStream, video: videoStream };
}


function attachAudioStream(stream){
    const audioElement = document.querySelector('#call audio');
    if (!audioElement) return;

    audioElement.srcObject = stream;
    audioElement.autoplay = true;
    console.log(stream)

    audioElement.play().catch(err => {
        console.warn("Autoplay blocked, user interaction may be required:", err);
    });

}

function attachVideoStream(stream, videoElement) {
    if (!(stream instanceof MediaStream)) return;
    if (!(videoElement instanceof HTMLVideoElement)) {
        console.warn("Provided element is not a video element");
        return;
    }

    stream.getVideoTracks()[0].addEventListener("mute", (event) => {
        console.log(event);
    });

    videoElement.srcObject = stream;
    videoElement.autoplay = true;
    videoElement.playsInline = true;
    videoElement.muted = true; // for local preview
    videoElement.play().catch(err => console.warn('Video play failed:', err));

    // Hide overlay if any
    const overlay = videoElement?.closest?.('div')?.querySelector('.video-call-overlay');
    overlay?.classList.add('hidden');
    overlay?.querySelector('.user-image').classList.add('hidden');
    overlay?.querySelector('.buffering').classList.add('hidden');
}


export function cameraOnOff(status) {
    const call = document.getElementById('call');
    if (!call) return;

    const video = call.querySelector('video[x-ref="local"]');
    if (!video || !video.srcObject) return;

    const on = status === true || status === 'true' || status === 1 || status === '1';
    video.srcObject.getVideoTracks().forEach(track => {
        track.enabled = on;
    });

    if (!on) video.pause();
    else video.play().catch(err => console.warn('Video play failed:', err));

    // Overlay based on actual camera track state
    const overlay = video?.closest?.('div')?.querySelector('.video-call-overlay');
    if (overlay) {
        overlay.classList.toggle('hidden', on);
        overlay.querySelector('.user-image')?.classList.toggle('hidden', on);
    }
}






















function toggleOverlay(peerVideo, peerSettings, loading = false) {
    const overlay = peerVideo?.closest('div')?.querySelector('.video-call-overlay');
    const userImage = overlay?.querySelector('.user-image');
    const buffering = overlay?.querySelector('.buffering');

    const { camera } = peerSettings;
    if (camera) {
        overlay?.classList.add('hidden');
        userImage?.classList.add('hidden');
        buffering?.classList.add('hidden');
    } else {
        overlay?.classList.remove('hidden');
        userImage?.classList.remove('hidden');
        if(loading) buffering?.classList.remove('hidden');
    }
}















export async function start_webrtc_connection(wire, Call, peerSettings) {
    const call =  await wire?.callArray();
    const settings = call?.settings;

    const stream_id = settings?.stream;
    const stream = streams.get(stream_id);

    if (!stream || !(stream instanceof MediaStream)) {
        console.error('No local stream found for this call.');
        return;
    }

    try {
        // 1. Create RTCPeerConnection
        const connection = new RTCPeerConnection({
            iceServers: [
                {urls: 'stun:stun.l.google.com:19302'} // public STUN server
            ]
        });


        // 2. Add existing local tracks to the peer connection
        stream.getTracks().forEach(track => {
            connection.addTrack(track, stream)
        });


        // 3. Listen for remote tracks from the peer (Listener)
        connection.ontrack = (event) => {
            if (Call?.type === 'voice') {
                console.log(peerAudio);
                if (peerAudio && peerAudio.srcObject !== event.streams[0]) {
                    peerAudio.srcObject = event.streams[0];
                    peerAudio.play().catch(console.error);
                    // console.log("Remote audio stream received");
                }
            } else if (Call?.type === 'video') {
                const peerVideo = document.querySelector('#call video[x-ref="peer"]');
                if (peerVideo && peerVideo.srcObject !== event.streams[0]) {
                    peerVideo.srcObject = event.streams[0];
                    toggleOverlay(peerVideo, peerSettings); // show/hide overlay
                    peerVideo.play().catch(console.error);
                    // console.log("Remote video stream received");
                }
            }
        }

        // 4. Handle ICE candidates
        connection.onicecandidate = (event) => {
            if (event.candidate) {
                wire?.WS_send({
                    rtc: true,
                    type: 'ice-candidate',
                    candidate: event.candidate
                });
            }
        };

        // 5. Save the peer connection globally or in a Map for later use
        window.peerConnection = connection;


        // console.log('RTCPeerConnection created and local tracks added.');



        // 6. Send Offer
        if(auth?.user?.id === call?.caller?.id){
            await createOffer(wire, call);
        }

    } catch (error) {
        console.error(error);
    }
}





export async function createOffer(wire, call) {
    const connection = window.peerConnection;
    if (!connection) return console.error('PeerConnection not found.');

    try {
        // Create an SDP offer
        const offer = await connection.createOffer();
        await connection.setLocalDescription(offer);

        // Send offer to the receiver via Livewire WS
        wire?.WS_send({
            rtc: true,
            type: 'offer',
            sdp: offer
        });

        // console.log('Offer created and sent:', offer);
    } catch (err) {
        console.error('Failed to create offer:', err);
    }
}


export async function handleOffer(wire, offer) {
    const connection = window.peerConnection;
    if (!connection) return console.error('PeerConnection not found.');

    try {
        // Set remote description from caller
        await connection.setRemoteDescription(new RTCSessionDescription(offer));

        // Create answer
        const answer = await connection.createAnswer();
        await connection.setLocalDescription(answer);

        // Send answer back to caller
        wire?.WS_send({
            rtc: true,
            type: 'answer',
            sdp: answer
        });

        // console.log('Answer created and sent:', answer);
    } catch (err) {
        console.error('Failed to handle offer:', err);
    }
}



export async function handleAnswer(answer) {
    const connection = window.peerConnection;
    if (!connection) return console.error('PeerConnection not found.');

    try {
        if (connection.signalingState === 'have-local-offer') {
            await connection.setRemoteDescription(new RTCSessionDescription(answer));
            // console.log('Remote description set from answer.');
        } else {
            // Wait until local offer is set
            const onStateChange = async () => {
                if (connection.signalingState === 'have-local-offer') {
                    connection.removeEventListener('signalingstatechange', onStateChange);
                    await connection.setRemoteDescription(new RTCSessionDescription(answer));
                    // console.log('Remote description set from answer after local offer.');
                }
            };
            connection.addEventListener('signalingstatechange', onStateChange);
        }
    } catch (err) {
        console.error('Failed to handle answer:', err);
    }
}


export async function handleCandidate(candidate) {
    const connection = window.peerConnection;
    if (!connection) return console.error('PeerConnection not found.');

    try {
        await connection.addIceCandidate(new RTCIceCandidate(candidate));
        // console.log('Added remote ICE candidate:', candidate);
    } catch (err) {
        console.error('Failed to add ICE candidate:', err);
    }
}






export function manageRTC(data){
    const callElement = document.getElementById('call');
    const wire_id = callElement?.closest('div[wire\\:id]')?.getAttribute('wire:id');
    const wire = window?.Livewire.find(wire_id);

    if (!data?.rtc) return;
    const connection = window.peerConnection;
    if (data.type === 'offer') {
        if (connection){
            handleOffer(wire, data.sdp);
        } else {
            start_webrtc_connection(wire).then(() => {
                handleOffer(wire, data.sdp);
            });
        }
    } else if (data.type === 'answer') {
        if (connection && data.sdp){
            handleAnswer(data.sdp);
        }
    } else if (data.type === 'ice-candidate') {
        if(connection && data.candidate){
            handleCandidate(data.candidate);
        }
    } else {
        console.warn("Unknown RTC message type:", data.type);
    }
}

export function refreshPeerSettings(settings){
    const peerVideo = document.getElementById('call')?.querySelector('video[x-ref="peer"]');
    toggleOverlay(peerVideo, settings);
}



// Bind with window -----------------------------------------
Object.assign(window, {
    init_Call,
    setVideoFeedPosition,
    streams
});
