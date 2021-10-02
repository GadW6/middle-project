  // Variables
const uploadPic = document.querySelector('#formUpload')

  // Event Listeners
uploadPic.addEventListener('change', (e) => {
  const filePath = e.target.value
  const fileName = filePath.split('\\')[2]
  if (fileName) {
    document.querySelector('#formUpload .file-upload-wrapper').attributes["data-text"].value = fileName
  } else {
    document.querySelector('#formUpload .file-upload-wrapper').attributes["data-text"].value = 'Choose image'
  }
})