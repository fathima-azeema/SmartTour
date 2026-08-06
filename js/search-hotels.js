// Example AJAX for live hotel search (you can extend this for more dynamic features)
document.getElementById('search-btn').addEventListener('click', function() {
    let location = document.getElementById('location').value;
    let price_min = document.getElementById('price_min').value;
    let price_max = document.getElementById('price_max').value;

    // AJAX request to fetch filtered results
    fetch(`search-hotels.php?location=${location}&price_min=${price_min}&price_max=${price_max}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('hotel-results').innerHTML = data;
        });
});
