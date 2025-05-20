function updateStars(rating) {
    // Get all the star-rating containers
    const starContainers = document.querySelectorAll('.star-rating');

    starContainers.forEach((starContainer) => {
        let stars = '';

        // Loop through and add filled and empty stars
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '★'; // filled star
            } else {
                stars += '☆'; // empty star
            }
        }

        // Update each star container's inner HTML
        starContainer.innerHTML = stars;
    });
}

// Example: Update the stars for all the car listings
updateStars(4); // shows ★★★★☆
