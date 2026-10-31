
const searchInput = document.getElementById("searchInput");
const filterButtons = document.querySelectorAll(".filter-btn");
const menuCards = document.querySelectorAll(".menu-card");
const noResults = document.getElementById("noResults");

let selectedCategory = "all";

function filterThalis() {

    const searchValue = searchInput.value.toLowerCase();
    let visibleCount = 0;

    menuCards.forEach(card => {

        const name = card.dataset.name;
        const category = card.dataset.category;

        const matchesSearch = name.includes(searchValue);

        const matchesCategory =
            selectedCategory === "all" ||
            category === selectedCategory;

        if (matchesSearch && matchesCategory) {
            card.style.display = "block";
            visibleCount++;
        } else {
            card.style.display = "none";
        }

    });

    noResults.style.display =
        visibleCount === 0 ? "block" : "none";
}

searchInput.addEventListener("input", filterThalis);

filterButtons.forEach(button => {

    button.addEventListener("click", () => {

        filterButtons.forEach(btn =>
            btn.classList.remove("active")
        );

        button.classList.add("active");

        selectedCategory = button.dataset.category;

        filterThalis();

    });

});
