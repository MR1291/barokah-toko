import { createClient } from '@supabase/supabase-js'
import * as fs from 'fs'
import * as path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

// Load env from .env.local
const envPath = path.join(__dirname, '..', '.env.local')
const envContent = fs.readFileSync(envPath, 'utf-8')
const envVars = {}

envContent.split('\n').forEach(line => {
  const [key, value] = line.split('=')
  if (key && value) {
    envVars[key.trim()] = value.trim()
  }
})

const supabaseUrl = envVars['NEXT_PUBLIC_SUPABASE_URL']
const supabaseKey = envVars['NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY']

if (!supabaseUrl || !supabaseKey) {
  console.error('❌ Missing SUPABASE env vars')
  process.exit(1)
}

const supabase = createClient(supabaseUrl, supabaseKey)

async function parseCSV(filePath) {
  const content = fs.readFileSync(filePath, 'utf-8')
  const lines = content.split('\n').filter(line => line.trim())
  
  return lines.map(line => {
    const parts = line.split(',,')
    if (parts.length < 2) {
      const [col1, col2, col3, col4, col5, col6, col7] = line.split(',')
      return {
        kode_barang: col1?.trim() || '',
        barcode: col2?.trim() || '',
        nama_barang: col3?.trim() || '',
        harga1: parseFloat(col4?.replace(/[.,]/g, '')) || 0,
        harga2: parseFloat(col5?.replace(/[.,]/g, '')) || 0,
        stok: parseInt(col6) || 0,
        kategori: col7?.trim() || 'UMUM'
      }
    }
    return null
  }).filter(Boolean)
}

async function migrateToSupabase() {
  try {
    console.log('📥 Reading CSV file...')
    const csvPath = path.join(__dirname, '..', 'app', 'tabula-Stok.csv')
    const data = await parseCSV(csvPath)
    
    if (data.length === 0) {
      console.warn('⚠️  CSV is empty')
      return
    }
    
    console.log(`✅ Parsed ${data.length} rows from CSV`)
    
    // Clear existing data (optional)
    console.log('🧹 Clearing existing data...')
    await supabase.from('barangs').delete().neq('id', 0)
    
    // Insert in batches of 100
    const batchSize = 100
    for (let i = 0; i < data.length; i += batchSize) {
      const batch = data.slice(i, i + batchSize)
      const { error } = await supabase
        .from('barangs')
        .insert(batch)
      
      if (error) {
        console.error(`❌ Error inserting batch ${i / batchSize + 1}:`, error)
        continue
      }
      
      console.log(`✅ Inserted ${Math.min(batchSize, data.length - i)} rows`)
    }
    
    console.log(`\n🎉 Migration complete! ${data.length} items imported.`)
  } catch (err) {
    console.error('❌ Migration failed:', err.message)
    process.exit(1)
  }
}

migrateToSupabase()
