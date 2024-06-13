import feather from 'feather-icons';
import { createApp, ref, reactive, watch } from 'vue/dist/vue.esm-bundler.js';
import axios from 'axios';
import Pickr from '@simonwep/pickr';
import draggable from 'vuedraggable/src/vuedraggable';

import '@simonwep/pickr/dist/themes/classic.min.css';

window.axios = axios;
window.Vue = { createApp, ref, reactive, watch };
window.VueDraggable = draggable;

document.addEventListener('DOMContentLoaded', function () {
    createApp({
        setup() {
            const isCollapsed = ref(true);
            const isUserDropdownCollapsed = ref(true);

            window.addEventListener('click', event => {
                const ignore = ['navbar-toggler', 'navbar-toggler-icon', 'dropdown-toggle'];
                if (ignore.some(className => event.target.classList.contains(className))) return;
                if (!isCollapsed.value) isCollapsed.value = true;
                if (!isUserDropdownCollapsed.value) isUserDropdownCollapsed.value = true;
            });

            return {
                isCollapsed,
                isUserDropdownCollapsed,
            };
        }
    }).mount('.v-navbar');

    function findModal(key)
    {
        const modal = document.querySelector(`[data-modal=${key}]`);

        if (!modal) throw `Attempted to open modal '${key}' but no such modal found.`;

        return modal;
    }

    function openModal(modal)
    {
        setTimeout(function()
        {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }, 200);
    }

    document.querySelectorAll('[data-open-modal]').forEach(item =>
    {
        item.addEventListener('click', event =>
        {
            event.preventDefault();

            openModal(findModal(event.currentTarget.dataset.openModal));
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(modalClose =>
    {
        modalClose.addEventListener('click', event =>
        {
            event.preventDefault

            setTimeout(function()
            {
                modalClose.closest('[data-modal]').classList.remove('flex');
                modalClose.closest('[data-modal]').classList.add('hidden');
            }, 200);
        });
    });

    document.querySelectorAll('[data-dismiss]').forEach(item =>
    {
        item.addEventListener('click', event => event.currentTarget.parentElement.style.display = 'none');
    });

    const hash = window.location.hash.substr(1);
    if (hash.startsWith('modal='))
    {
        openModal(findModal(hash.replace('modal=','')));
    }

    feather.replace();

    const input = document.querySelector('input[name=color_light_mode]');

    if (!input) return;

    const pickr = Pickr.create({
        el: '.pickr',
        theme: 'classic',
        default: input.value || null,

        swatches: [
            window.defaultCategoryColor,
            '#f44336',
            '#e91e63',
            '#9c27b0',
            '#673ab7',
            '#3f51b5',
            '#2196f3',
            '#03a9f4',
            '#00bcd4',
            '#009688',
            '#4caf50',
            '#8bc34a',
            '#cddc39',
            '#ffeb3b',
            '#ffc107'
        ],

        components: {
            preview: true,
            hue: true,
            interaction: {
                input: true,
                save: true
            }
        },

        strings: {
            save: 'Apply'
        }
    });

    pickr.on('save', instance => pickr.hide())
        .on('clear', instance =>
        {
            input.value = '';
            input.dispatchEvent(new Event('change'));
        })
        .on('cancel', instance =>
        {
            const selectedColor = instance
                .getSelectedColor()
                .toHEXA()
                .toString();

            input.value = selectedColor;
            input.dispatchEvent(new Event('change'));
        })
        .on('change', (color, instance) =>
        {
            const selectedColor = color
                .toHEXA()
                .toString();

            input.value = selectedColor;
            input.dispatchEvent(new Event('change'));
        });
});
