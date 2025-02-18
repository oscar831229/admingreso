<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Admin\Menu;
use App\Models\Officials\Person;
use Illuminate\Support\Facades\Schema;
use App\Models\Tracing\Meeting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer("layouts.belectronica.left_panel", function ($view) {
            $menus = Menu::getMenu(true);
            $view->with('menusComposer', $menus);
        });

        View::composer("layouts.commitment.left_panel", function ($view) {
            $request = request();

            # OBTENER TOKEN
            $token = $request->route('token');
            $person = Person::where(['token_notification' => $token])->first();

            # OBTENER MEETING
            $meeting_id = $request->route('meeting_id');
            $meeting = Meeting::find($meeting_id);

            $meetings =  Person::getCommitment($person, $meeting->year);

            $view->with('meetingsComposer', $meetings);

        });

        Schema::defaultStringLength(191); 
    }
}
