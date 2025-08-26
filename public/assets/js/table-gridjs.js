// Ensure all containers are cleared before rendering
const clearAndRenderGrid = (containerId, gridConfig) => {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = ""; // Clear the container
        new gridjs.Grid(gridConfig).render(container);
    }
};

// Update all grid initializations
clearAndRenderGrid("table-gridjs", {
    columns: [
        { name: "ID", width: "80px", formatter: e => gridjs.html('<span class="fw-semibold">' + e + "</span>") },
        { name: "Name", width: "150px" },
        { name: "Email", width: "220px", formatter: e => gridjs.html('<a href="">' + e + "</a>") },
        { name: "Position", width: "250px" },
        { name: "Company", width: "180px" },
        { name: "Country", width: "180px" }
    ],
    pagination: { limit: 5 },
    sort: true,
    search: true,
    data: [
    ]
});

