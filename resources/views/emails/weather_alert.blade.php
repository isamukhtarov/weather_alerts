@component('mail::message')
    # Weather Alert Summary
    @foreach($data as $city => $info)
        ## {{ $city }}
        - **Precipitation:** {{ $info['precipitation'] }} mm
        - **UV Index:** {{ $info['uv_index'] }}
    @endforeach
    Stay safe!
@endcomponent
