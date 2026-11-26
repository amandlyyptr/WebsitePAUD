function previewImg(input, targetId) {
    var file = input.files[0];
    var img  = document.getElementById(targetId);

    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB!');
            input.value = '';
            img.classList.add('d-none');
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            img.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        img.classList.add('d-none');
    }
}