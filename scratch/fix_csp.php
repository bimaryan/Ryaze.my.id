<?php
$f = 'd:/Ryaze.my.id/resources/views/pages/hosting/user/database/index.blade.php';
$c = file_get_contents($f);

// 1. Script tag replacement
$c = str_replace('<script>', '<script nonce="{{ app(\'csp_nonce\') }}">', $c);

// Line 48: onclick="openCreateModal()"
$c = str_replace('onclick="openCreateModal()" class="inline-flex', 'id="btn-open-create-modal" class="inline-flex', $c);

// Line 68: onclick="confirmDelete('{{ route('user_hosting.databases.destroy', $db->hashid) }}')"
$c = preg_replace('/onclick="confirmDelete\(\'([^\']+)\'\)"\s*class="([^"]+)"/', 'class="$2 btn-delete-db" data-action="$1"', $c);

// Line 81 & 92 & 106: onclick="copyToClipboard('...')"
$c = preg_replace('/onclick="copyToClipboard\(\'([^\']+)\'\)"\s*class="([^"]+)"/', 'class="$2 btn-copy" data-copy="$1"', $c);

// Line 103: onclick="togglePass('pass-{{ $db->hashid }}', this)"
$c = preg_replace('/onclick="togglePass\(\'([^\']+)\', this\)"\s*class="([^"]+)"/', 'class="$2 btn-toggle-pass" data-target="$1"', $c);

// Line 157 & 218: onclick="closeCreateModal()"
$c = str_replace('onclick="closeCreateModal()"', 'class="btn-close-modal"', $c);
$c = str_replace('class="btn-close-modal" class="', 'class="btn-close-modal ', $c); // Cleanup if any double class

// Line 198: onclick="generatePassword()"
$c = str_replace('onclick="generatePassword()" class="text-xs', 'id="btn-generate-password" class="text-xs', $c);

// Line 206: onclick="copyModalPassword()"
$c = preg_replace('/onclick="copyModalPassword\(\)"\s*class="([^"]+)"/', 'id="btn-copy-modal-password" class="$1"', $c);

// Also need to append the event listener logic to the LAST <script> block
$script_append = <<<EOD
    // ── CSP Compliant Event Listeners ──────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        // Create Modal Open
        const btnOpenModal = document.getElementById('btn-open-create-modal');
        if (btnOpenModal) btnOpenModal.addEventListener('click', openCreateModal);

        // Create Modal Close
        document.querySelectorAll('.btn-close-modal').forEach(btn => {
            btn.addEventListener('click', closeCreateModal);
        });

        // Delete DB
        document.querySelectorAll('.btn-delete-db').forEach(btn => {
            btn.addEventListener('click', (e) => {
                confirmDelete(e.currentTarget.getAttribute('data-action'));
            });
        });

        // Copy
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.addEventListener('click', (e) => {
                copyToClipboard(e.currentTarget.getAttribute('data-copy'));
            });
        });

        // Toggle Password
        document.querySelectorAll('.btn-toggle-pass').forEach(btn => {
            btn.addEventListener('click', (e) => {
                togglePass(e.currentTarget.getAttribute('data-target'), e.currentTarget);
            });
        });

        // Generate Password
        const btnGenPass = document.getElementById('btn-generate-password');
        if (btnGenPass) btnGenPass.addEventListener('click', generatePassword);

        // Copy Modal Password
        const btnCopyModalPass = document.getElementById('btn-copy-modal-password');
        if (btnCopyModalPass) btnCopyModalPass.addEventListener('click', copyModalPassword);
    });
</script>
EOD;

// find the last occurrence of </script> to replace
$pos = strrpos($c, '</script>');
if($pos !== false) {
    $c = substr_replace($c, $script_append, $pos, strlen('</script>'));
}

file_put_contents($f, $c);
echo "Done CSP Fix";
