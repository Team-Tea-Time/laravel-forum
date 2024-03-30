import { formatDistance } from 'date-fns';
import '@melloware/coloris/dist/coloris.css';
import Coloris from '@melloware/coloris';
import NestedSort from 'nested-sort';

window.dateFormatDistance = formatDistance;
window.Coloris = Coloris;
window.Coloris.init();
window.NestedSort = NestedSort;
