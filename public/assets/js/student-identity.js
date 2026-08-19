// Student Identity Verification — Tazkira upload preview & drag-drop

function previewFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewName').textContent = input.files[0].name;
            document.getElementById('dropContent').style.display = 'none';
            document.getElementById('previewContainer').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('dropZone').classList.remove('dragover');
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const input = document.getElementById('tazkiraInput');
        input.files = files;
        previewFile(input);
    }
}
