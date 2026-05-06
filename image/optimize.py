import os
from PIL import Image
import sys

def optimize_images(directory):
    max_size = (1600, 1600)
    saved_bytes = 0
    for filename in os.listdir(directory):
        if filename.lower().endswith(('.png', '.jpg', '.jpeg')):
            filepath = os.path.join(directory, filename)
            original_bytes = os.path.getsize(filepath)
            try:
                with Image.open(filepath) as img:
                    original_size = img.size
                    img.thumbnail(max_size, Image.Resampling.LANCZOS)
                    
                    if filename.lower().endswith('.png'):
                        # If it's a huge PNG that has no transparency, we could convert to JPEG, but let's be safe.
                        img.save(filepath, format='PNG', optimize=True)
                    else:
                        if img.mode in ('RGBA', 'P'):
                            img = img.convert('RGB')
                        img.save(filepath, format='JPEG', quality=80, optimize=True)
                
                new_bytes = os.path.getsize(filepath)
                saved_bytes += (original_bytes - new_bytes)
                print(f"Optimized {filename}: {original_size} -> {img.size} ({original_bytes // 1024}KB -> {new_bytes // 1024}KB)")
            except Exception as e:
                print(f"Failed to optimize {filename}: {e}")
    print(f"Total space saved: {saved_bytes // (1024 * 1024)} MB")

if __name__ == '__main__':
    optimize_images('.')
