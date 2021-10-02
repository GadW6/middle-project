  // Variables
const radios = document.querySelectorAll('form#add-post input[type="radio"]')
const urlField = document.querySelector('#field-url')
const uploadField = document.querySelector('#field-upload')
const cancelBtn = document.querySelector('#cancel-btn')
const modal = document.querySelector('.modal')
const textArea = document.querySelector('#input-body');
const textTiny = document.querySelector('[data-tiny-editor]');


// Helper Function
const setFieldURL = () => {
  if (urlField.classList.contains('d-none')) {
    urlField.classList.remove('d-none')
    uploadField.classList.add('d-none')
  }
}
const setFieldUpload = () => {
  if (uploadField.classList.contains('d-none')) {
    uploadField.classList.remove('d-none')
    urlField.classList.add('d-none')
  }
} 
const setNone = () => {
  if (!urlField.classList.contains('d-none')) {
    urlField.classList.add('d-none')
  } else if (!uploadField.classList.contains('d-none')) {
    uploadField.classList.add('d-none')
  }
}


  // Event Listeners
radios.forEach(radio => {
  radio.addEventListener('click', () => {
    if (radio.id === 'btn-url') {
      setFieldURL()
    } else if (radio.id === 'btn-upload') {
      setFieldUpload()
    }
  })
})

uploadField.addEventListener('change', (e) => {
  const pathArr = e.target.value.split('\\').reverse()
  const el = pathArr[0]
  if (el) {
    uploadField.firstElementChild.firstElementChild.lastElementChild.textContent = el
  } else {
    uploadField.firstElementChild.firstElementChild.lastElementChild.textContent = 'Upload image..'
  }
})

cancelBtn.addEventListener('click', (e) => {
  modal.classList.remove('d-none')
  modal.classList.add('d-flex')
  e.preventDefault()
})

modal.addEventListener('click', (e) => {
  if(e.target.classList.contains('close-modal')) {
    modal.classList.remove('d-flex')
    modal.classList.add('d-none')
  } else if (e.target.classList.contains('cancel-modal')) {
    window.location.replace("/blog.php")
  } 
  e.preventDefault()
})

textTiny.addEventListener('input', e => {
  textArea.innerHTML = e.target.innerHTML
});