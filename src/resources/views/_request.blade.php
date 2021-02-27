<form method="post" action="{{ $url }}" name="netopia" target="_self">
    <input type="hidden" name="env_key" value="{{ $envKey }}"/>
    <input type="hidden" name="data" value="{{ $data }}"/>

    @if($autoRedirect)
        <p>{{ __('You will be redirected to the secure payment page') }}</p>
        <p>
            <img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif" alt="" title=""
                 onload="javascript:document.netopia.submit()">
        </p>
    @endif
        <button type="submit">
            {{ __('Proceed to Payment') }}
        </button>
</form>
