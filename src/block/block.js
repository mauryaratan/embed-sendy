/**
 * BLOCK: embed-sendy
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* global esdBlockSettings */

//  Import CSS.
import './style.scss';
import './editor.scss';
import icon from './icon';
import { autop } from '@wordpress/autop';

const {
	InspectorControls,
	ColorPalette,
} = wp.blocks;

const {
	SelectControl,
	PanelBody,
	PanelColor,
} = wp.components;

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'embed-sendy/block-embed-sendy', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Embed Sendy' ), // Block title.
	description: __( 'Displays a form for Sendy mailing list.' ),
	icon: icon, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'Sendy' ),
		__( 'form' ),
		__( 'newsletter' ),
	],

	attributes: {
		list: {
			type: 'string',
			default: esdBlockSettings.default_list,
		},
		formBackgroundColor: {
			type: 'string',
			default: '#f5f5f5',
		},
		formTextColor: {
			type: 'string',
			default: '#000000',
		},
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Block props.
	 * @returns {Array} Array of Block control and block.
	 */
	edit: function( { attributes: { list, formBackgroundColor, formTextColor }, className, setAttributes } ) {
		const formHeader = autop( esdBlockSettings.form_header );
		const formFooter = autop( esdBlockSettings.form_footer );

		return ( [
			<InspectorControls key="inspector">
				<PanelBody>
					<SelectControl
						label={ __( 'Mailing List' ) }
						description={ __( 'Choose mailing list to use for the subscription form.' ) }
						value={ list }
						options={ JSON.parse( esdBlockSettings.lists ) }
						onChange={ ( value ) => setAttributes( { list: value } ) }
					/>

					<PanelColor
						title={ __( 'Form Background Color' ) }
						colorValue={ formBackgroundColor }
						initialOpen={ false }
					>
						<ColorPalette
							label={ __( 'Form Background Color' ) }
							value={ formBackgroundColor }
							onChange={ ( value ) => setAttributes( { formBackgroundColor: value } ) }
							colors={ [ '#00d1b2', '#3373dc', '#209cef', '#22d25f', '#ffdd57', '#ff3860', '#7941b6', '#392F43' ] }
						/>
					</PanelColor>

					<PanelColor
						title={ __( 'Text Color' ) }
						colorValue={ formTextColor }
						initialOpen={ false }
					>
						<ColorPalette
							label={ __( 'Background Color' ) }
							value={ formTextColor }
							onChange={ ( value ) => setAttributes( { formTextColor: value } ) }
							colors={ [ '#32373c', '#fff' ] }
						/>
					</PanelColor>

				</PanelBody>
			</InspectorControls>,

			<form method="post" id="js-esd-form" className={ 'esd-form ' + className } key="block-field" style={ {
				backgroundColor: formBackgroundColor,
				color: formTextColor,
			} }>
				{ formHeader && (
					<div className="esd-form__row esd-form__header" dangerouslySetInnerHTML={ { __html: formHeader } }></div>
				) }
				<div className="esd-form__row esd-form__fields">
					<input type="email" name="email" placeholder="Enter your email" readOnly />
					<input type="submit" value="Subscribe" disabled="true" />
				</div>
				{ formFooter && (
					<div className="esd-form__row esd-form__footer" dangerouslySetInnerHTML={ { __html: formFooter } }></div>
				) }
			</form>,
		] );
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @returns {undefined}
	 */
	save: function() {
		// Rendering in PHP.
		return null;
	},
} );
