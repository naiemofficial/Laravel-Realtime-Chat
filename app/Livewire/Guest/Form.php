<?php

namespace App\Livewire\Guest;

use App\Http\Controllers\GuestController;
use App\Http\Middleware\GuestCookie;
use App\Models\Cookie;
use App\Models\Guest;
use Livewire\Component;
use Illuminate\Support\Facades\Cookie as CookieFacade;

class Form extends Component
{
    public $name;
    private $mounted = [
        'name' => null,
    ];

    public $isValidGuest = false;
    public $currentGuest;
    private $cookie;

    public function mount(){
        $this->currentGuest = Guest::current();
        $this->isValidGuest = $this->currentGuest?->validity() ?? false;
        $this->mounted['name'] = $this->currentGuest?->name;
        $this->name = $this->mounted['name'];

        $this->dispatch('guest.updated', $this->currentGuest);
    }

    public function submit(){
        request()->merge(['name' => $this->name]);
        if(Guest::isValid()){
            // Update if mounted name and current name isn't same
            if($this->name != $this->mounted['name']){
                app(GuestController::class)->update(request(), Guest::current());
                $this->currentGuest = Guest::current();
            }
        } else {
            // Add as new
            $GuestCookie = new GuestCookie();
            $response = $GuestCookie->handle(request(), fn($request) => $request);

            // Response Data and Get Cookie
            $response_data = $response->getData();
            $this->cookie = Cookie::find($response_data->cookie);

            // Re-check Guest validation
            $this->currentGuest = Guest::current($this->cookie);
            $this->isValidGuest = $this->currentGuest?->validity() ?? false;
            $this->name = $this->currentGuest?->name;
        }

        $this->dispatch('guest.updated', $this->currentGuest);
    }


    public function render()
    {
        return view('livewire.guest.form');
    }
}
