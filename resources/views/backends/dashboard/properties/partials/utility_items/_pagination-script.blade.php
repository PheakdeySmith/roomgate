<script>
    // Enhanced AJAX pagination for mobile optimization
    document.addEventListener('DOMContentLoaded', function() {
        // Get the container that holds the pagination
        const paginationContainer = document.querySelector('.meter-history-table').parentNode;
        
        // If found, set up AJAX pagination
        if (paginationContainer) {
            paginationContainer.addEventListener('click', function(e) {
                // Target pagination links
                if (e.target.tagName === 'A' && e.target.classList.contains('page-link')) {
                    e.preventDefault();
                    
                    // Get the URL from the clicked link
                    const url = e.target.getAttribute('href');
                    if (url) {
                        // Show loading indicator
                        const historyTable = document.querySelector('.meter-history-table');
                        historyTable.classList.add('opacity-50');
                        
                        // Make AJAX request
                        fetch(url)
                            .then(response => response.text())
                            .then(html => {
                                // Create a temporary element to parse the HTML
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = html;
                                
                                // Get the new content
                                const newHistoryTable = tempDiv.querySelector('.meter-history-table');
                                const newPagination = tempDiv.querySelector('.d-flex.justify-content-center');
                                
                                if (newHistoryTable && newPagination) {
                                    // Replace the old content with the new content
                                    historyTable.innerHTML = newHistoryTable.innerHTML;
                                    document.querySelector('.d-flex.justify-content-center').innerHTML = newPagination.innerHTML;
                                    
                                    // Remove loading indicator
                                    historyTable.classList.remove('opacity-50');
                                    
                                    // Scroll to the top of the history table with smooth scrolling
                                    historyTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching pagination:', error);
                                historyTable.classList.remove('opacity-50');
                            });
                    }
                }
            });
        }
    });
</script>
