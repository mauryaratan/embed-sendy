/**
 * BLOCK: embed-sendy
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* global esdBlockSettings */
import { autop } from '@wordpress/autop';
import classnames from 'classnames';
import Controls from './controls';
import './editor.scss';
import icon from './icon';
import './style.scss';

const { Fragment } = wp.element;

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { Disabled } = wp.components;

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
	description: __( 'Displays a subscription form for Sendy mailing list.' ),
	icon: icon, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'Sendy' ),
		__( 'form' ),
		__( 'newsletter' ),
	],

	attributes: {
		name: {
			type: 'checkbox',
			default: false,
		},
		gdpr: {
			type: 'checkbox',
			default: false,
		},
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
	edit( props ) {
		const { attributes: { formBackgroundColor, formTextColor, name, gdpr }, className } = props;

		const formHeader = autop( esdBlockSettings.form_header );
		const formFooter = autop( esdBlockSettings.form_footer );

		return (
			<Fragment>
				<Controls { ...props } />

				<Disabled>

					<form method="post" id="js-esd-form" className={ classnames( 'esd-form', className, {
						'esd-form--show-name': !! ( name || gdpr ),
					} ) } key="block-field" style={ {
						backgroundColor: formBackgroundColor,
						color: formTextColor,
					} }>
						{ formHeader && (
							<div className="esd-form__row esd-form__header" dangerouslySetInnerHTML={ { __html: formHeader } }></div>
						) }
						<div className="esd-form__row esd-form__fields">
							{ name && (
								<input type="text" name="name" placeholder="Name" readOnly />
							) }
							<input type="email" name="email" placeholder="Enter your email" readOnly />

							{ gdpr && (
								<div className="gdpr-row">
									<input type="checkbox" id="gdpr" name="gdpr" readOnly />
									<label htmlFor="gdpr">{ esdBlockSettings.gdpr_text }</label>
								</div>
							) }
							<input type="submit" value="Subscribe" />
						</div>
						{ formFooter && (
							<div className="esd-form__row esd-form__footer" dangerouslySetInnerHTML={ { __html: formFooter } }></div>
						) }
					</form>
				</Disabled>
			</Fragment>
		);
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
	save() {
		// Rendering in PHP.
		return null;
	},
} );
