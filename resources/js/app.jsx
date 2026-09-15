import '../css/app.css';
import './echo';
import Quill from 'quill';
window.Quill = Quill;

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route as ziggyRoute } from 'ziggy-js';
import { Ziggy } from './ziggy';

window.route = (name, params, absolute) => ziggyRoute(name, params, absolute, Ziggy);

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#4f46e5',
    },
});
