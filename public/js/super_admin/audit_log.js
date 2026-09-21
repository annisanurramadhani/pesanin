document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('audit-detail-modal');
    const backdrop = document.getElementById('audit-modal-backdrop');
    const modalContent = document.getElementById('audit-modal-content');
    const closeButton = document.getElementById('audit-modal-close');
    const closeBottomButton = document.getElementById('audit-modal-close-bottom');

    if (!modal) {
        return;
    }

    const actionElement = document.getElementById('audit-modal-action');
    const userElement = document.getElementById('audit-modal-user');
    const dateElement = document.getElementById('audit-modal-date');
    const modelElement = document.getElementById('audit-modal-model');
    const idElement = document.getElementById('audit-modal-id');
    const ipElement = document.getElementById('audit-modal-ip');
    const agentElement = document.getElementById('audit-modal-agent');
    const oldValuesElement = document.getElementById('audit-modal-old');
    const newValuesElement = document.getElementById('audit-modal-new');

    const detailButtons = document.querySelectorAll('.audit-detail-btn');

    const escapeHtml = (value) => {
        if (value === null || value === undefined) {
            return '-';
        }

        const div = document.createElement('div');
        div.textContent = String(value);

        return div.innerHTML;
    };

    const formatValue = (value) => {
        if (value === null || value === undefined) {
            return '-';
        }

        if (typeof value === 'object') {
            return JSON.stringify(value, null, 2);
        }

        return String(value);
    };

    const openModal = () => {
        modal.classList.remove('hidden');

        requestAnimationFrame(() => {
            if (backdrop) {
                backdrop.classList.remove('opacity-0');
            }

            if (modalContent) {
                modalContent.classList.remove(
                    'opacity-0',
                    'scale-95'
                );

                modalContent.classList.add(
                    'opacity-100',
                    'scale-100'
                );
            }
        });

        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        if (backdrop) {
            backdrop.classList.add('opacity-0');
        }

        if (modalContent) {
            modalContent.classList.remove(
                'opacity-100',
                'scale-100'
            );

            modalContent.classList.add(
                'opacity-0',
                'scale-95'
            );
        }

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    };

    const setValues = (element, value) => {
        if (!element) {
            return;
        }

        if (
            value === null ||
            value === undefined ||
            value === '' ||
            (typeof value === 'object' &&
                Object.keys(value).length === 0)
        ) {
            element.textContent = '-';
            return;
        }

        element.textContent = formatValue(value);
    };

    const loadAuditDetail = (button) => {
        const data = button.dataset;

        let oldValues = {};
        let newValues = {};

        try {
            oldValues = data.old
                ? JSON.parse(data.old)
                : {};
        } catch (error) {
            console.error(
                'Gagal membaca old_values audit log:',
                error
            );
        }

        try {
            newValues = data.new
                ? JSON.parse(data.new)
                : {};
        } catch (error) {
            console.error(
                'Gagal membaca new_values audit log:',
                error
            );
        }

        if (actionElement) {
            actionElement.textContent = data.action || '-';
        }

        if (userElement) {
            userElement.textContent = data.user || '-';
        }

        if (dateElement) {
            dateElement.textContent = data.date || '-';
        }

        if (modelElement) {
            modelElement.textContent = data.model || '-';
        }

        if (idElement) {
            idElement.textContent = data.modelId || '-';
        }

        if (ipElement) {
            ipElement.textContent = data.ip || '-';
        }

        if (agentElement) {
            agentElement.textContent = data.agent || '-';
        }

        setValues(oldValuesElement, oldValues);
        setValues(newValuesElement, newValues);

        openModal();
    };

    detailButtons.forEach((button) => {
        button.addEventListener('click', () => {
            loadAuditDetail(button);
        });
    });

    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    if (closeBottomButton) {
        closeBottomButton.addEventListener(
            'click',
            closeModal
        );
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeModal);
    }

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            !modal.classList.contains('hidden')
        ) {
            closeModal();
        }
    });
});