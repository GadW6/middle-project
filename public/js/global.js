  // Variables
const toggler = document.querySelector('button.navbar-toggler')

  // Event Listeners
toggler.addEventListener('click', (e) => {
  if (e.target.classList[1] === 'fa-bars') {
    e.target.classList.remove('fa-bars')
    e.target.classList.add('fa-times')
  } else if (e.target.classList[1] === 'fa-times') {
    e.target.classList.remove('fa-times')
    e.target.classList.add('fa-bars')
  }
  e.preventDefault()
})