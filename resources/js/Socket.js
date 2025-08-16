import MessageSent from "./app/Events.js";
Echo.private(`conversation-connection.${auth.user.id}`).listen('ConversationConnection', async (response) => {
    MessageSent(response);
});
