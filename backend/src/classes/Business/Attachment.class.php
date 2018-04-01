<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2017-03-02 15:27:31                    *
 *   This file will never be generated again - feel free to edit.            *
 *****************************************************************************/

	class Attachment extends AutoAttachment implements Prototyped, DAOConnected
	{
        const CLEANUP_DELAY_AFTER_DELETED = '2 sec';
        const MAX_FILE_SIZE = 10485760; // 10 Mb

        /**
		 * @return Attachment
		**/
		public static function create()
		{
			return new self;
		}
		
		/**
		 * @return AttachmentDAO
		**/
		public static function dao()
		{
			return Singleton::getInstance('AttachmentDAO');
		}
		
		/**
		 * @return ProtoAttachment
		**/
		public static function proto()
		{
			return Singleton::getInstance('ProtoAttachment');
		}

        public function getImageByRole(AttachmentImageRole $role)
        {
            /** @var AttachmentImage[] $images */
            $images = $this->getImages()->getList();
            foreach ($images as $image) {
                if ($image->getRole()->is($role)) {
                    return $image;
                }
            }
            return null;
		}

        public function export()
        {
            $imagesCacheKey = __CLASS__ . ':images:' . $this->getId();
            $images = APCU_ENABLED ? apcu_fetch($imagesCacheKey) : null;
            if (!is_array($images)) {
                $images = [];
                foreach (AttachmentImageRole::getList() as $role) {
                    $image = $this->getImageByRole($role);
                    if ($image) {
                        $images[$role->getName()] = $image->export();
                    }
                }
                if (APCU_ENABLED) {
                    apcu_store($imagesCacheKey, $images, 300);
                }
            }

            if (count($images)) {
                $viewerIp = RequestUtils::getRealIp(App::me()->getRequest(), false);
                foreach ($images as $i => $image) {
                    $image['url'] = AttachmentImage::secureWebPath($image['server'], $image['path'], $viewerIp);
                    $images[$i] = $image;
                }
            }

            return [
                'id'        => $this->getId(),
                'images'    => $images,
                'embed'     => $this->getEmbed() ? $this->getEmbed()->export() : null,
                'isPublished'=> $this->isPublished(),
                'isNsfw'    => $this->isNsfw(),
                'isDeleted' => $this->isDeleted() || ($this->isPublished() && $this->getPost()->isDeleted()),
            ];
		}
	}
?>