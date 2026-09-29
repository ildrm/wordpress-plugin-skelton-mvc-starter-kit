wp.blocks.registerBlockType('starterkit-mvc/greeting', {
  edit: () => wp.element.createElement('p', { className: 'starterkit-mvc-greeting' }, wp.i18n.__('Greeting appears on the published page.', 'wordpress-mvc-starter')),
  save: () => null,
});
