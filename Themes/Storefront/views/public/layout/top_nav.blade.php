<section class="top-nav-wrap">
    <div class="container">
        <div class="top-nav">
            <div class="row justify-content-between">
                <div class="top-nav-left d-none d-lg-block">
                    <span>
   &nbsp;<b>Free</b> shipping &nbsp;&nbsp;&nbsp; 
   <b>Pay in</b> 3 settlements &nbsp;&nbsp;&nbsp; 
  <b>Free</b> returns
</span>

                </div>

                <div class="top-nav-right">
                    <ul class="list-inline top-nav-right-list">
                        <li>
                            <a href="{{ route('contact.create') }}">
                                {{ trans('storefront::layout.contact') }}
                            </a>
                        </li>

                        <li class="top-nav-compare">
                            <a href="{{ route('compare.index') }}">
                                <i class="las la-random"></i>
                                {{ trans('storefront::layout.compare') }}
                            </a>
                        </li>

                        @if (is_multilingual())
                            <li>
                                <i class="las la-globe"></i>
                                <select class="custom-select-option arrow-black" onchange="location = this.value">
                                    @foreach (supported_locales() as $locale => $language)
                                        <option value="{{ localized_url($locale) }}" {{ locale() === $locale ? 'selected' : '' }}>
                                            {{ $language['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </li>
                        @endif

                        @if (is_multi_currency())
                            <li>
                               
                                <select class="custom-select-option arrow-black" onchange="location = this.value">
                                    @foreach (setting('supported_currencies') as $currency)
                                        <option
                                            value="{{ route('current_currency.store', ['code' => $currency]) }}"
                                            {{ currency() === $currency ? 'selected' : '' }}
                                        >
                                            {{ $currency }}
                                        </option>
                                    @endforeach
                                </select>
                            </li>
                        @endif

                        @auth
<li>
    <a href="{{ route('account.dashboard.index') }}">
        <i class="lar la-user"></i>
<?php

// Get the authenticated user's first name
$userFirstName = auth()->user()->first_name;

// Get the user's IP address
$userIP = $_SERVER['REMOTE_ADDR'];

try {
    // Fetch user's timezone from an online GeoIP service
    $timezone = file_get_contents("http://ip-api.com/json/$userIP?fields=timezone");
    $timezone = json_decode($timezone)->timezone;
    
    // Set the timezone for accurate time calculation
    date_default_timezone_set($timezone);
    
    // Get the current hour
    $currentHour = date('H');

    // Determine the appropriate greeting based on the time of day
    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = 'Good morning';
    } elseif ($currentHour >= 12 && $currentHour < 18) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
    
    // Output the greeting along with the user's name
    echo "$greeting <b>$userFirstName</b>";
} catch (Exception $e) {
    // Handle any errors
    echo 'Error: ' . $e->getMessage();
}

?>


    </a>
</li>

                        @else
                            <li>
                                <a href="{{ route('login') }}">
                                   <i class="las la-angle-right"></i>
                                    {{ trans('storefront::layout.login') }}
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>