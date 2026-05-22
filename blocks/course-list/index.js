( function () {
	wp.blocks.registerBlockType( 'bantu-learn/course-list', {
		edit: function () {
			return wp.element.createElement(
				'div',
				{ style: { padding: '16px', background: '#f0f4f8', borderRadius: '6px', textAlign: 'center', color: '#232F47' } },
				'Course List — displays all active courses on the frontend.'
			);
		},
		save: function () {
			return null; // server-side rendered
		},
	} );
} )();
