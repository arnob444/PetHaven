document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const category = document.querySelector('select[name="category"]').value;
            const age = document.querySelector('input[name="age"]').value;
            if (!category && !age) {
                alert('Please select a category or enter an age to search.');
                e.preventDefault();
            }
        });
    }
});