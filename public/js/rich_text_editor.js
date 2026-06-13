(function () {
    const editorRegistry = new Map();

    const isEditorBlank = (surface) => {
        const text = (surface.textContent || '').replace(/\u00a0/g, ' ').trim();
        return text === '';
    };

    const textToHtml = (text) => {
        const lines = String(text || '').replace(/\r\n/g, '\n').split('\n');
        return lines
            .map((line) => line.trim() === '' ? '<p><br></p>' : `<p>${escapeHtml(line)}</p>`)
            .join('');
    };

    const escapeHtml = (text) => String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const syncEditor = (root) => {
        const surface = root.querySelector('.edu-rich-editor-surface');
        const input = root.querySelector('.edu-rich-editor-input');

        if (!surface || !input) return;

        input.value = isEditorBlank(surface) ? '' : surface.innerHTML.trim();
        input.dispatchEvent(new Event('input', { bubbles: true }));
        root.dispatchEvent(new CustomEvent('richtext:change', {
            bubbles: true,
            detail: {
                isBlank: input.value === '',
                value: input.value
            }
        }));
    };

    const setEditorText = (inputId, text) => {
        const root = editorRegistry.get(inputId);
        if (!root) return false;

        const surface = root.querySelector('.edu-rich-editor-surface');
        if (!surface) return false;

        surface.innerHTML = textToHtml(text);
        syncEditor(root);
        surface.focus();
        return true;
    };

    const runCommand = (root, command, value = null) => {
        const surface = root.querySelector('.edu-rich-editor-surface');
        if (!surface) return;

        surface.focus();
        document.execCommand('styleWithCSS', false, true);

        if (command === 'fontSize') {
            document.execCommand('fontSize', false, value || '3');
        } else {
            document.execCommand(command, false, value);
        }

        syncEditor(root);
    };

    const initEditor = (root) => {
        if (root.dataset.richReady === '1') return;
        root.dataset.richReady = '1';

        const surface = root.querySelector('.edu-rich-editor-surface');
        const input = root.querySelector('.edu-rich-editor-input');
        if (!surface || !input) return;

        if (input.id) {
            editorRegistry.set(input.id, root);
        }

        if (input.value.trim() !== '' && surface.innerHTML.trim() === '') {
            surface.innerHTML = input.value;
        }

        root.querySelectorAll('[data-rich-command]').forEach((control) => {
            if (control.tagName.toLowerCase() === 'select') {
                control.addEventListener('change', () => {
                    runCommand(root, control.dataset.richCommand, control.value);
                    control.selectedIndex = 0;
                });
                return;
            }

            control.addEventListener('click', (event) => {
                event.preventDefault();
                runCommand(root, control.dataset.richCommand, control.dataset.richValue || null);
            });
        });

        surface.addEventListener('input', () => syncEditor(root));
        surface.addEventListener('blur', () => syncEditor(root));
        surface.addEventListener('paste', () => {
            window.setTimeout(() => syncEditor(root), 0);
        });

        syncEditor(root);
    };

    const initAllEditors = () => {
        document.querySelectorAll('.edu-rich-editor').forEach(initEditor);
    };

    document.addEventListener('DOMContentLoaded', () => {
        initAllEditors();

        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', () => {
                window.EduQuizRichText.syncAll();
            });
        });
    });

    window.EduQuizRichText = {
        initAll: initAllEditors,
        syncAll() {
            document.querySelectorAll('.edu-rich-editor').forEach(syncEditor);
        },
        setText: setEditorText
    };
})();
