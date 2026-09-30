/**
 * Text box of the step block in the editor.
 *
 * Background, border, shadow and padding of a step belong to its text box,
 * not to the step itself. block.json keeps the editor from putting them on the
 * block wrapper; this hook turns the same attributes into class and style for
 * the inner element. On the front end jgor_st_step_box_attributes() does the
 * same in PHP.
 */

/*
 * WordPress offers no stable helpers for block supports that skip
 * serialization. Its own blocks, the button for one, use these four. Should
 * one of them go away, the fallbacks below keep the editor working; only the
 * preview of the box then misses that part of the styling.
 */
/* eslint-disable @wordpress/no-unsafe-wp-apis */
import {
	__experimentalGetShadowClassesAndStyles as getShadowProps,
	__experimentalGetSpacingClassesAndStyles as getSpacingProps,
	__experimentalUseBorderProps as useBorderPropsCore,
	__experimentalUseColorProps as useColorPropsCore,
} from '@wordpress/block-editor';
/* eslint-enable @wordpress/no-unsafe-wp-apis */

const EMPTY_PROPS = {};

/**
 * Stands in for a helper that WordPress does not provide.
 *
 * @return {Object} Props without class and style.
 */
const noProps = () => EMPTY_PROPS;

const useBorderProps = useBorderPropsCore ?? noProps;
const useColorProps = useColorPropsCore ?? noProps;
const getShadow = getShadowProps ?? noProps;
const getSpacing = getSpacingProps ?? noProps;

/**
 * Returns class and style of the text box of a step.
 *
 * @param {Object} attributes Attributes of the step block.
 * @return {{className: string, style: Object}} Props for the text box.
 */
export function useTextBoxProps( attributes ) {
	const { backgroundColor, style } = attributes;

	// Only the background belongs to the box, the text colour stays on the step.
	const colorProps = useColorProps( {
		backgroundColor,
		style: { color: { background: style?.color?.background } },
	} );
	const borderProps = useBorderProps( attributes );

	// Only the padding belongs to the box, the margin stays on the step.
	const spacingProps = getSpacing( {
		style: { spacing: { padding: style?.spacing?.padding } },
	} );
	const shadowProps = getShadow( attributes );

	return {
		className: [
			'jgor-st-step__text',
			colorProps.className,
			borderProps.className,
		]
			.filter( Boolean )
			.join( ' ' ),
		style: {
			...borderProps.style,
			...colorProps.style,
			...spacingProps.style,
			...shadowProps.style,
		},
	};
}
