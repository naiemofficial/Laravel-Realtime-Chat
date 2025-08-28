import {
    checkLocalMediaPermissions,
    executeDropMessage,
    startVoiceStream,
    startVideoStream,
    stopVoiceStream,
    stopVideoStream
} from "./custom/script.js";

if(typeof Livewire === 'object'){
    Livewire.on('execute-drop-message', data => {
        if(typeof executeDropMessage === 'function'){
            executeDropMessage('livewire', data);
        }
    });

    Livewire.on('request-for-media-permission', async data => {
        // const localMedia = await checkLocalMediaPermissions('voice');
    });

    Livewire.on('JS-start-voice-call', async data => {
        const conversation_id = data?.conversation_id ?? null;
        const {mic} = await checkLocalMediaPermissions('voice');
        if(mic){
            Livewire.dispatch('start-voice-call', {conversation_id: conversation_id});
        }
    });

    Livewire.on('JS-start-video-call', async data => {
        const conversation_id = data?.conversation_id ?? null;
        const {mic, camera} = await checkLocalMediaPermissions('video');
        if(mic && camera){
            Livewire.dispatch('start-video-call', {conversation_id: conversation_id});
        }
    });


    Livewire.on('start-voice-stream', () => {
        startVoiceStream();
    });


    Livewire.on('start-video-stream', () => {
        startVideoStream();
    });


    Livewire.on('stop-voice-stream', () => {
        stopVoiceStream();
    });


    Livewire.on('stop-video-stream', () => {
        stopVideoStream();
    });
}

window.addEventListener('log-test', () => {
    console.log('Test');
});
