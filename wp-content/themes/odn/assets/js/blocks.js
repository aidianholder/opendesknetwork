/**
 * Editor side of the theme's blocks. Plain ES5 + wp globals, no build step.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'odn/network-badge', {
		apiVersion: 3,
		title: __( 'Network Badge', 'odn' ),
		description: __( 'Links back to the Open Desk Network home. Hidden on the network’s main site.', 'odn' ),
		category: 'theme',
		icon: 'admin-site-alt3',
		attributes: {
			tone: { type: 'string', default: 'dark' },
			showOnMain: { type: 'boolean', default: false },
		},
		supports: { align: [ 'left', 'center', 'right' ], typography: { fontSize: true } },
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
						el( SelectControl, {
							label: __( 'Mark colour', 'odn' ),
							value: a.tone,
							options: [
								{ label: __( 'Dark (for light backgrounds)', 'odn' ), value: 'dark' },
								{ label: __( 'Light (for dark backgrounds)', 'odn' ), value: 'light' },
							],
							onChange: function ( v ) {
								props.setAttributes( { tone: v } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show on the network’s main site', 'odn' ),
							checked: a.showOnMain,
							onChange: function ( v ) {
								props.setAttributes( { showOnMain: v } );
							},
						} )
					)
				),
				el( ServerSideRender, { block: 'odn/network-badge', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
