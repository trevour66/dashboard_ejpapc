const palettes = {
	palette_one: "palette1",
	palette_two: "palette2",
	palette_three: "palette3",
	palette_four: "palette4",
	palette_five: "palette5",
	palette_six: "palette6",
	palette_seven: "palette7",
	palette_eight: "palette8",
	palette_nine: "palette9",
	palette_ten: "palette10",
};

const randomPalette = () => {
	const paletteKeys = Object.keys(palettes);
	const randomPaletteKey =
		paletteKeys[Math.floor(Math.random() * paletteKeys.length)];
	const randomPaletteValue = palettes[randomPaletteKey];

	return randomPaletteValue ?? palettes.palette_ten;
};

export { palettes, randomPalette };
