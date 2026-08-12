document.addEventListener('DOMContentLoaded', function () {
    const packageDuration = document.getElementById('package_duration_id');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const price = document.getElementById('price');

    if (!packageDuration || !startDate || !endDate || !price) {
        return;
    }

    function calculateSubscription() {
        const selectedOption = packageDuration.options[packageDuration.selectedIndex];
        const days = parseInt(selectedOption?.dataset.days || 0);
        const selectedPrice = selectedOption?.dataset.price || '';

        price.value = selectedPrice;

        if (!startDate.value || !days) {
            endDate.value = '';
            return;
        }

        const [year, month, day] = startDate.value.split('-').map(Number);

        const date = new Date(year, month - 1, day);

        if (isNaN(date.getTime())) {
            endDate.value = '';
            return;
        }

        date.setDate(date.getDate() + days - 1);

        const endYear = date.getFullYear();
        const endMonth = String(date.getMonth() + 1).padStart(2, '0');
        const endDay = String(date.getDate()).padStart(2, '0');

        endDate.value = `${endYear}-${endMonth}-${endDay}`;
    }

    packageDuration.addEventListener('change', calculateSubscription);
    startDate.addEventListener('change', calculateSubscription);

    calculateSubscription();
});
