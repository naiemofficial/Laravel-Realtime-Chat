Echo.private(`conversation.${auth.user.id}`).listen('MessageSent', async (response) => {
    if(response?.Conversation && response?.Sender && response?.Message){
        Livewire.dispatch('refresh-conversations');

        const message = response.Message;
        if (message.type !== 'starter') {
            if (typeof executeDropMessage === 'function') {
                const response = await executeDropMessage('websocket', message);
                if(response.status){
                    const data = response.data;
                    Livewire.dispatch('refresh-conversations');
                    Livewire.dispatch('seen-conversation-incoming-message', { openedConversation: data.openedConversation });
                }
            }
        }
    }

});
