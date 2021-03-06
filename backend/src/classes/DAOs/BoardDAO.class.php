<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-04-16 15:41:40                    *
 *   This file will never be generated again - feel free to edit.            *
 *****************************************************************************/

	class BoardDAO extends AutoBoardDAO implements IdentifiableByRequestDAO
	{
		/** @return Board */
		public function getByDir($dir) {
			return $this->getByLogic(
                Expression::eq('dir', $dir)
			);
		}

        /** @return Board */
        public function getByRequestedValue($value) {
            if ($value === '' || $value === null) {
                throw new ObjectNotFoundException;
            }

            $board = $this->getByDir($value);

            if ($board->isDeleted()) {
                throw new ObjectNotFoundException();
            }

            return $board;
        }

    }
?>