
<script>
    google.maps.event.addDomListener(window, 'load', initialize);
    function initialize() {
        var input = document.getElementById('locationSearch');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            // place variable will have all the information you are looking for.
            $('#lat').val(place.geometry['location'].lat());
            $('#lng').val(place.geometry['location'].lng());
        });

        var input = document.getElementById('dropOffLocationSearch');
        var autocompleteDropOff = new google.maps.places.Autocomplete(input);
        autocompleteDropOff.addListener('place_changed', function () {
            var place = autocompleteDropOff.getPlace();
            // place variable will have all the information you are looking for.
            $('#drop_off_lat').val(place.geometry['location'].lat());
            $('#drop_off_lng').val(place.geometry['location'].lng());
        });
    }
</script>

