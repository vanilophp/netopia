<form method="POST" action="{{ $url }}" name="mobilpay">
        <input type="hidden" name="env_key" value="{{ $envKey }}"/>
        <input type="hidden" name="data" value="{{ $data }}"/>

        <button type="submit" class="btn btn-primary">Pay safely</button>
    </form>
    <!-- Hey I am very unique !-->
</form>
