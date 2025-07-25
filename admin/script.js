const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});

// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})

if(window.innerWidth < 768) {
	sidebar.classList.add('hide');
}

window.addEventListener('resize', function () {
	if(this.innerWidth < 768) {
		sidebar.classList.add('hide');
	} else {
		sidebar.classList.remove('hide');
	}
})

const switchMode = document.getElementById('switch-mode');

// Set the initial switch state according to the body class
switchMode.checked = document.body.classList.contains('dark');

switchMode.addEventListener('change', function () {
    if (this.checked) {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }

    // Submit the form to update the session value
    document.getElementById('dark-mode-form').submit();
});

// minimize
function toggleOrderContent() {
    let orderContainer = document.querySelector('.table-data .order');
    orderContainer.classList.toggle('collapsed');
}
function toggleTodoContent() {
    let orderContainer = document.querySelector('.table-data .todo');
    orderContainer.classList.toggle('collapsed');
}

// minimize