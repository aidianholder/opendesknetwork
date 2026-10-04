/**
 * Editor side of the network blocks. Plain ES5 + wp globals, no build step.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'odn/publisher-directory', {
		apiVersion: 3,
		title: __( 'Publisher Directory', 'odn' ),
		description: __( 'Lists every publisher on the network with their latest story.', 'odn' ),
		category: 'widgets',
		icon: 'groups',
		attributes: {
			number: { type: 'integer', default: 24 },
			showLatest: { type: 'boolean', default: true },
		},
		supports: { align: [ 'wide', 'full' ], spacing: { margin: true, padding: true } },
		edit: function ( props ) {
			var a = props.attributes;
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Settings', 'odn' ) },
						el( RangeControl, {
							label: __( 'Number of publishers', 'odn' ),
							value: a.number,
							min: 1,
							max: 100,
							onChange: function ( v ) {
								props.setAttributes( { number: v } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show latest story', 'odn' ),
							checked: a.showLatest,
							onChange: function ( v ) {
								props.setAttributes( { showLatest: v } );
							},
						} )
					)
				),
				el( ServerSideRender, { block: 'odn/publisher-directory', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'odn/join-form', {
		apiVersion: 3,
		title: __( 'Join Form', 'odn' ),
		description: __( 'Application form for journalists. Submissions are emailed to the network and saved under Applications. Works on the main site only.', 'odn' ),
		category: 'widgets',
		icon: 'email-alt',
		supports: { align: [ 'wide' ], spacing: { margin: true, padding: true } },
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el( ServerSideRender, { block: 'odn/join-form' } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
