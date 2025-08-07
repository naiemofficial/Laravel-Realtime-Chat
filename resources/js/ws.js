if(typeof Livewire != "undefined"){
    Livewire.on('guest.updated', (currentGuest) => {
        let guest = currentGuest[0];

        console.log(guest);
        if(guest){
            console.log(`App.Models.Guest.${guest.id}`);
            Echo
                .private(`App.Models.Guest.${guest.id}`)
                .listen('MessageSent', (event) => {
                    console.log(event);
                });
        }
    });
} else {
    Echo
        .private(`App.Models.User.1`)
        .listen('MessageSent', (event) => {
            console.log(event);
        });
}


