import {
    checkMicCameraPermission,
    requestForMicCameraPermission,
    executeDropMessage,
    startMicStream,
    stopCameraStream,
    startVideoStream, validateMicStream, validateCameraStream, stopMicStream, validateStreams, stopStream,
    validateStream, cameraOnOff, start_webrtc_connection, handleOffer, handleAnswer, handleCandidate
} from "./custom/script.js";

if(typeof Livewire === 'object'){
    Livewire.on('execute-drop-message', data => {
        if(typeof executeDropMessage === 'function'){
            executeDropMessage('livewire', data);
        }
    });

    Livewire.on('request-for-mic-camera-permission', async type => {
        type = Array.isArray(type) ? type[0] : type;
        await requestForMicCameraPermission(type);
    });

    Livewire.on('JS-start-voice-call', async data => {
        const conversation_id = data?.conversation_id ?? null;

        let {mic: has_mic_permission} = await checkMicCameraPermission();
        if(!has_mic_permission){
            const micStream = await requestForMicCameraPermission('mic');
            has_mic_permission = validateMicStream(micStream);
            stopMicStream(micStream);
        }

        if(has_mic_permission){
            Livewire.dispatch('start-voice-call', {conversation_id: conversation_id});
        } else {
            Livewire.dispatch('refresh-message-alert', {response: { error: 'There\'s a problem with mic permission' }} );
        }
    });

    Livewire.on('JS-start-video-call', async data => {
        const conversation_id = data?.conversation_id ?? null;

        let stream = null;
        let {mic: has_mic_permission, camera: has_camera_permission} = await checkMicCameraPermission();
        if(!has_mic_permission || !has_camera_permission){
            stream = await requestForMicCameraPermission(null, true);
            ({mic: has_mic_permission, camera: has_camera_permission} = validateStream(stream));
            stopStream(stream)
        }

        if(has_mic_permission && has_camera_permission){
            Livewire.dispatch('start-video-call', {conversation_id: conversation_id});
        } else {
            Livewire.dispatch('refresh-message-alert', {response: { error: 'There\'s a problem with mic/camera permission' }} );
        }
    });


    Livewire.on('start-voice-stream', async () => {
        await startMicStream();
    });


    Livewire.on('start-video-stream', async () => {
        await startVideoStream();
    });


    Livewire.on('stop-voice-stream', () => {
        stopMicStream();
    });


    Livewire.on('stop-video-stream', () => {
        stopCameraStream();
    });


    Livewire.on('stop-stream', (stream) => {
        stream = Array.isArray(stream) ? stream[0] : stream;
        stopStream(stream);
    });


    Livewire.on('camera-on-off', (status) => {
        status = Array.isArray(status) ? status[0] : status;
        cameraOnOff(status);
    });


    Livewire.on('start-webrtc-connection', (call) => {
        call = Array.isArray(call) ? call[0] : call;
        start_webrtc_connection(call);
    });

    Livewire.on('webrtc-message', (data) => {
        if (data.type === 'offer') handleOffer(data.sdp);
        else if (data.type === 'answer') handleAnswer(data.sdp);
        else if (data.type === 'ice-candidate') handleCandidate(data.candidate);
    });
}

window.addEventListener('log-test', () => {
    console.log('Test');
});
