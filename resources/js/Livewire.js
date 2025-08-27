import {executeDropMessage, getLocalMedia} from "./custom/script.js";

if(typeof Livewire === 'object'){
    Livewire.on('execute-drop-message', data => {
        if(typeof executeDropMessage === 'function'){
            executeDropMessage('livewire', data);
        }
    });

    Livewire.on('request-for-media-permission', callType => {
        const localMedia = getLocalMedia(callType);
        console.log(localMedia);
    });
}

window.addEventListener('log-test', () => {
    console.log('Test');
});
