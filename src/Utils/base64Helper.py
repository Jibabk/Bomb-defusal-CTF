import base64

def encode_base64(text, times=32):
    result = text.encode("utf-8")

    for _ in range(times):
        result = base64.b64encode(result)

    return result.decode("utf-8")

def decode_base64(text, times=32):
    result = text.encode("utf-8")

    for _ in range(times):
        result = base64.b64decode(result)

    return result.decode("utf-8")

def save_to_file(filename, content):
    with open(filename, "w", encoding="utf-8") as file:
        file.write(content)


def read_from_file(filename):
    with open(filename, "r", encoding="utf-8") as file:
        return file.read().strip()

while True:
    print("\n=== Base64 32 Times ===")
    print("1 - Encode and save to file")
    print("2 - Decode from file")
    print("0 - Exit")

    option = input("Choose an option: ")

    if option == "1":
        text = input("Enter the text to encode: ")
        output_file = input("Enter the output file name: ")

        result = encode_base64(text)
        save_to_file(output_file, result)

        print(f"\nEncoded result saved to: {output_file}")
        print(f"Result length: {len(result)} characters")

    elif option == "2":
        input_file = input("Enter the file name to decode: ")

        try:
            text = read_from_file(input_file)
            result = decode_base64(text)

            print("\nDecoded result:")
            print(result)

        except FileNotFoundError:
            print("\nError: file not found.")

        except Exception:
            print("\nError while decoding.")
            print("Make sure the file contains text encoded in Base64 exactly 64 times.")

    elif option == "0":
        print("Exiting...")
        break

    else:
        print("Invalid option.")
