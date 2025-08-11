Echo.private(`conversation.${auth.user.id}`).listen('MessageSent', (response) => {
    const data = response.message;
    if(typeof executeDropMessage === 'function'){
        executeDropMessage(data);
    }
});
