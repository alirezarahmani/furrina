<?php

namespace Furrina\Storage;

class ImageDefinition
{
    const FORMAT_JPG = 'jpg';
    const FORMAT_PNG = 'png';
    const FORMAT_GIF = 'gif';

    const WATERMARK_POSITION_BOTTOM_RIGHT = 'bottom-right';
    const WATERMARK_POSITION_BOTTOM_LEFT = 'bottom-left';
    const WATERMARK_POSITION_TOP_LEFT = 'top-left';
    const WATERMARK_POSITION_TOP_RIGHT = 'top-right';
    const WATERMARK_POSITIONS = [
        self::WATERMARK_POSITION_BOTTOM_RIGHT,
        self::WATERMARK_POSITION_BOTTOM_LEFT,
        self::WATERMARK_POSITION_TOP_LEFT,
        self::WATERMARK_POSITION_TOP_RIGHT
    ];
    const WATERMARK_POSITION_RANDOM = 'random';

    private $maxWidth = 0;
    private $maxHeight = 0;
    private $finalWidth;
    private $finalHeight;
    private $quality = 100;
    private $exactWidth = 0;
    private $exactHeight = 0;
    private $format = self::FORMAT_JPG;
    private $watermarkFile;
    private $watermarkPosition = self::WATERMARK_POSITION_RANDOM;
    private $watermarkMargin = 0;
    private $watermarkMaxWidthScale = 75;
    private $watermarkMaxHeightScale = 25;

    public function __construct(string $format = 'jpg', $quality = 100)
    {
        $this->format = $format;
        $this->quality = $quality;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function setMaxWidthHeight(int $maxWidth, int $maxHeight): self
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        return $this;
    }

    public function setMaxWidth(int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = 100000;
        return $this;
    }

    public function getFinalWidth(): int
    {
        return $this->finalWidth;
    }

    public function getFinalHeight(): int
    {
        return $this->finalHeight;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = $quality;
        return $this;
    }

    public function getExactWidth(): int
    {
        return $this->exactWidth;
    }

    public function getExactHeight(): int
    {
        return $this->exactHeight;
    }

    public function getWatermarkFile(): ?string
    {
        return $this->watermarkFile;
    }

    public function getWatermarkPosition(): string
    {
        return $this->watermarkPosition;
    }

    public function getWatermarkMargin(): int
    {
        return $this->watermarkMargin;
    }

    public function getWatermarkMaxWidthScale(): int
    {
        return $this->watermarkMaxWidthScale;
    }

    public function getWatermarkMaxHeightScale(): int
    {
        return $this->watermarkMaxHeightScale;
    }

    public function setWatermarkFile(
        string $watermarkFile,
        string $position = 'random',
        int $margin = 10,
        int $watermarkMaxWidthScale = 75,
        int $watermarkMaxHeightScale = 25
    ): self {
        $this->watermarkFile = $watermarkFile;
        $this->watermarkPosition = $position;
        $this->watermarkMargin = $margin;
        $this->watermarkMaxWidthScale = $watermarkMaxWidthScale;
        $this->watermarkMaxHeightScale = $watermarkMaxHeightScale;
        return $this;
    }

    public function setExactWidthHeight(int $exactWidth, int $exactHeight): self
    {
        $this->exactWidth = $exactWidth;
        $this->exactHeight = $exactHeight;
        return $this;
    }
}
