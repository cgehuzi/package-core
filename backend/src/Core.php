<?php

declare(strict_types=1);

namespace Cgehuzi\Core;

/**
 * Метаданные пакета. VERSION синхронизируется с релизным тегом вручную при выпуске
 * (см. CHANGELOG.md). Используется, например, в /api/core/health для диагностики.
 */
final class Core
{
    public const VERSION = '0.0.1';
}
