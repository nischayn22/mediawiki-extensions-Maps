<?php
/**
 * Class with public static methods to handle shared parameter definitions
 * based on interfaces.
 *
 * @since 2.0
 *
 * @deprecated This needs to be refactored away since ItemParameterManipulation is deprecated
 */
abstract class MapsCommonParameterManipulation extends ItemParameterManipulation {


	/**
	 * This method requires that parameters are positionally correct,
	 * 1. Title
	 * 2. Text
	 * 3. Link
	 * 4. Stroke data (three parameters)
	 * 5. Fill data (two parameters)
	 * e.g ...title~text~link~strokeColor~strokeOpacity~strokeWeight~fillColor~fillOpacity
	 * @static
	 * @param $obj
	 * @param $metadataParams
	 */
	protected function handleCommonParams( array &$params , &$model ) {
		//Handle bubble and link parameters
		if ( $model instanceof iBubbleMapElement && $model instanceof iLinkableMapElement ) {
			$this->setBubbleDataFromParameter( $model , $params );
			$link = trim( array_shift( $params ) );
			$this->setLinkFromParameter( $model , $link );
		} else if ( $model instanceof iLinkableMapElement ) {
			//only supports links
			array_splice( $params, 0, 2 );
			$link = array_shift( $params );
			$this->setLinkFromParameter( $model , $link );
		} else if ( $model instanceof iBubbleMapElement ) {
			//only supports bubbles
			$this->setBubbleDataFromParameter( $model , $params );
			array_splice( $params, 2, 1 );
		}

		//handle stroke parameters
		if ( $model instanceof iStrokableMapElement ) {
			if ( $color = array_shift( $params ) ) {
				$model->setStrokeColor( $color );
			}

			if ( $opacity = array_shift( $params ) ) {
				$model->setStrokeOpacity( $opacity );
			}

			if ( $weight = array_shift( $params ) ) {
				$model->setStrokeWeight( $weight );
			}
		}

		//handle fill parameters
		if ( $model instanceof iFillableMapElement ) {
			if ( $fillColor = array_shift( $params ) ) {
				$model->setFillColor( $fillColor );
			}

			if ( $fillOpacity = array_shift( $params ) ) {
				$model->setFillOpacity( $fillOpacity );
			}
		}

		//handle hover parameter
		if ( $model instanceof iHoverableMapElement ) {
			if ( $visibleOnHover = array_shift( $params ) ) {
				$model->setOnlyVisibleOnHover( filter_var( $visibleOnHover , FILTER_VALIDATE_BOOLEAN ) );
			}
		}
	}

	private function setBubbleDataFromParameter( &$model , &$params ) {
		if ( $title = array_shift( $params ) ) {
			$model->setTitle( $title );
		}
		if ( $text = array_shift( $params ) ) {
			$model->setText( $text );
		}
	}

	private function setLinkFromParameter( &$model , $link ) {
		if( $link ) {
			if ( filter_var( $link , FILTER_VALIDATE_URL , FILTER_FLAG_SCHEME_REQUIRED ) ) {
				$model->setLink( $link );
			} else {
				$title = Title::newFromText( $link );
				$model->setLink( $title->getFullURL() );
			}
		}
	}
}
