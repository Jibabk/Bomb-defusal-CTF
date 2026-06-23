import hashlib

word = input("Enter the word: ")
salt = input("Enter the salt: ")
output_file = input("Enter the output filename: ")

text = word + salt

md5_hash = hashlib.md5(text.encode("utf-8")).hexdigest()

with open(output_file, "w", encoding="utf-8") as file:
    file.write(md5_hash)

print("\nHash generated successfully!")
print(f"Saved to: {output_file}")
print(f"MD5: {md5_hash}")
