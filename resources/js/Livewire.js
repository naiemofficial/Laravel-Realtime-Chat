import {executeDropMessage} from "./custom/functions.js";

Livewire.on('execute-drop-message', data => {
    if(typeof executeDropMessage === 'function'){
        executeDropMessage('livewire', data);
    }
});

window.addEventListener('log-test', () => {
    console.log('Test');
});
