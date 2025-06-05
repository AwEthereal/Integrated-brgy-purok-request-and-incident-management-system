// Handle emoji rating selection
document.addEventListener('DOMContentLoaded', function() {
    // Add click event to document for event delegation
    document.addEventListener('click', function(e) {
        // Check if clicked element or its parent has emoji-option class
        const emojiOption = e.target.closest('.emoji-option');
        if (!emojiOption) return;
        
        // Find the radio input
        const radio = emojiOption.closest('label').querySelector('input[type="radio"]');
        if (!radio) return;
        
        // Get all emojis in the same group
        const groupName = radio.name;
        const allEmojis = document.querySelectorAll(`input[name="${groupName}"]`);
        
        // Remove highlight from all emojis in this group
        allEmojis.forEach(r => {
            const parentLabel = r.closest('label');
            if (parentLabel) {
                const emojiDiv = parentLabel.querySelector('.emoji-option');
                if (emojiDiv) {
                    emojiDiv.classList.remove('bg-gray-100', 'scale-110', 'ring-2', 'ring-blue-300');
                }
            }
        });
        
        // Check the radio and highlight the emoji
        radio.checked = true;
        emojiOption.classList.add('bg-gray-100', 'scale-110', 'ring-2', 'ring-blue-300');
    });
    
    // Initialize any previously selected emojis on page load
    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        const parentLabel = radio.closest('label');
        if (parentLabel) {
            const emojiOption = parentLabel.querySelector('.emoji-option');
            if (emojiOption) {
                emojiOption.classList.add('bg-gray-100', 'scale-110', 'ring-2', 'ring-blue-300');
            }
        }
    });
});
