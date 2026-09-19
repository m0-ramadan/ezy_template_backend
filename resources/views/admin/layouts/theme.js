// Sidebar navigation toggle
document.querySelectorAll('.groupbtn').forEach(b => b.addEventListener('click', () => {
    const g = b.closest('.navgroup');
    if (g) g.classList.toggle('open');
}));
const mt = document.getElementById('mobileToggle'), sb = document.getElementById('sidebar');
if (mt && sb) mt.addEventListener('click', () => sb.classList.toggle('show'));

// Auto-initialize Quill rich text editor for all textarea.rich-editor elements
function initRichEditors() {
    if (typeof Quill === 'undefined') return;

    document.querySelectorAll('textarea.rich-editor').forEach(textarea => {
        if (textarea.dataset.quillInitialized) return;
        textarea.dataset.quillInitialized = 'true';

        // Create container for Quill
        const wrapper = document.createElement('div');
        wrapper.className = 'quill-wrapper';
        wrapper.style.marginBottom = '14px';

        const editorContainer = document.createElement('div');
        editorContainer.className = 'quill-editor-container';
        wrapper.appendChild(editorContainer);

        // Hide original textarea
        textarea.style.display = 'none';
        textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

        const toolbarOptions = [
            [{ 'header': [1, 2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'align': [] }, { 'direction': 'rtl' }],
            ['blockquote', 'code-block'],
            ['link', 'clean']
        ];

        const quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: textarea.placeholder || 'Enter detailed content here...',
            modules: {
                toolbar: toolbarOptions
            }
        });

        if (textarea.getAttribute('dir') === 'rtl') {
            quill.root.setAttribute('dir', 'rtl');
            quill.root.style.textAlign = 'right';
            quill.root.style.fontFamily = "'Cairo', 'Inter', sans-serif";
        }

        // Set initial HTML
        if (textarea.value && textarea.value.trim().length > 0) {
            quill.root.innerHTML = textarea.value;
        }

        // Sync to textarea on change
        quill.on('text-change', () => {
            textarea.value = quill.root.innerHTML;
        });

        // Sync before form submission
        const form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                textarea.value = quill.root.innerHTML;
            });
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRichEditors);
} else {
    initRichEditors();
}