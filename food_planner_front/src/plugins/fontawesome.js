// src/plugins/fontawesome.js
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons'; // Solid icons
import { fab } from '@fortawesome/free-brands-svg-icons'; // Brand icons (optional)

// Add icons to the library
library.add(fas, fab);

export default FontAwesomeIcon;
