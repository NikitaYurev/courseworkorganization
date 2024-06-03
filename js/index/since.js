document.addEventListener('DOMContentLoaded', function() {
    function updateTimeInExistence() {
        const startDate = new Date('2024-05-19T13:00:00+02:00'); // German time (CET/CEST)
        const now = new Date();
        const diff = now - startDate;

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        document.getElementById('time-in-existence').innerText = 
            `${days}d ${hours}h ${minutes}m ${seconds}s`; // Corrected typo in innerText
    }

    setInterval(updateTimeInExistence, 1000);
    updateTimeInExistence(); // Initial call to set the value immediately
});
