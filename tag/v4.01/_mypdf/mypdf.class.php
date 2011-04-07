<?php
/**
 * Logiciel : HTML2PDF - classe MyPDF
 * 
 * Convertisseur HTML => PDF
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 * @version		4.01
 */

if (!defined('__CLASS_MYPDF__'))
{
	define('__CLASS_MYPDF__', true);
	
	require_once(dirname(__FILE__).'/tcpdf_config_html2pdf.php');
	require_once(dirname(__FILE__).'/../_tcpdf/tcpdf.php');
	
	class MyPDF extends TCPDF
	{
		protected $footer_param	= array();
		protected $transf		= array();
		
		public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false)
		{
			parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
			$this->SetCreator(PDF_CREATOR);
			$this->SetAutoPageBreak(false, 0);
			$this->linestyleCap = '2 J';
			$this->setPrintHeader(false);
			$this->jpeg_quality=90;
			$this->SetMyFooter();
		}
		
		public function SetMyFooter($page = null, $date = null, $heure = null, $form = null)
		{
			$page	= ($page ? true : false);
			$date	= ($date ? true : false);
			$heure	= ($heure ? true : false);
			$form	= ($form ? true : false);
			
			$this->footer_param = array('page' => $page, 'date' => $date, 'heure' => $heure, 'form' => $form);
		}
		
		public function Footer()
		{ 
			$txt = '';
			if ($this->footer_param['form'])	$txt = (HTML2PDF::textGET('pdf05'));
			if ($this->footer_param['date'] && $this->footer_param['heure'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf03'));
			if ($this->footer_param['date'] && !$this->footer_param['heure'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf01'));
			if (!$this->footer_param['date'] && $this->footer_param['heure'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf02'));
			if ($this->footer_param['page'])	$txt.= ($txt ? ' - ' : '').(HTML2PDF::textGET('pdf04'));
			
			if (strlen($txt)>0)
			{
				$txt = str_replace('[[date_d]]',	date('d'),			$txt);
				$txt = str_replace('[[date_m]]',	date('m'),			$txt);
				$txt = str_replace('[[date_y]]',	date('Y'),			$txt);
				$txt = str_replace('[[date_h]]',	date('H'),			$txt);
				$txt = str_replace('[[date_i]]',	date('i'),			$txt);
				$txt = str_replace('[[date_s]]',	date('s'),			$txt);
				$txt = str_replace('[[current]]',	$this->PageNo(),	$txt);
				$txt = str_replace('[[nb]]',		'{nb}',				$txt);
				
				parent::SetY(-11);
        		$this->SetFont('helvetica', 'I', 8);
				$this->Cell(0, 10, $txt, 0, 0, 'R');
			}
		}

		public function cloneFontFrom(&$pdf)
		{
			$this->fonts			= &$pdf->getFonts();
			$this->FontFiles		= &$pdf->getFontFiles();
			$this->diffs			= &$pdf->getDiffs();
			$this->fontlist			= &$pdf->getFontList();
			$this->numfonts			= &$pdf->getNumFonts();
			$this->fontkeys			= &$pdf->getFontKeys();
			$this->font_obj_ids		= &$pdf->getFontObjIds();
			$this->annotation_fonts	= &$pdf->getAnnotFonts();
		}
		
		public function &getFonts() 		{ return $this->fonts; }
		public function &getFontFiles()		{ return $this->FontFiles; }
		public function &getDiffs() 		{ return $this->diffs; }
		public function &getFontList()		{ return $this->fontlist; }
		public function &getNumFonts()		{ return $this->numfonts; }
		public function &getFontKeys()		{ return $this->fontkeys; }
		public function &getFontObjIds()	{ return $this->font_obj_ids; }
		public function &getAnnotFonts()	{ return $this->annotation_fonts; }
		
		public function isLoadedFont($fontkey)
		{
			if (isset($this->fonts[$fontkey]))
				return true;
				
			if (isset($this->CoreFonts[$fontkey]))
				return true;
				
			return false;
		}
		
		public function setWordSpacing($ws=0.)
		{
			$this->ws = $ws;
			$this->_out(sprintf('%.3F Tw',$ws*$this->k));
		}
		
		public function clippingPathOpen($x = null, $y = null, $w = null, $h = null, $coin_TL=null, $coin_TR=null, $coin_BL=null, $coin_BR=null)
		{
			$path = '';
			if ($x!==null && $y!==null && $w!==null && $h!==null)
			{
				$x1 = $x*$this->k;
				$y1 = ($this->h-$y)*$this->k;

				$x2 = ($x+$w)*$this->k;
				$y2 = ($this->h-$y)*$this->k;

				$x3 = ($x+$w)*$this->k;
				$y3 = ($this->h-$y-$h)*$this->k;

				$x4 = $x*$this->k;
				$y4 = ($this->h-$y-$h)*$this->k;
				
				if ($coin_TL || $coin_TR || $coin_BL || $coin_BR)
				{
					if ($coin_TL) { $coin_TL[0] = $coin_TL[0]*$this->k; $coin_TL[1] =-$coin_TL[1]*$this->k; }
					if ($coin_TR) { $coin_TR[0] = $coin_TR[0]*$this->k; $coin_TR[1] =-$coin_TR[1]*$this->k; }
					if ($coin_BL) { $coin_BL[0] = $coin_BL[0]*$this->k; $coin_BL[1] =-$coin_BL[1]*$this->k; }
					if ($coin_BR) { $coin_BR[0] = $coin_BR[0]*$this->k; $coin_BR[1] =-$coin_BR[1]*$this->k; }

					$MyArc = 4/3 * (sqrt(2) - 1);
					
					if ($coin_TL)
						$path.= sprintf('%.2F %.2F m ', $x1+$coin_TL[0], $y1);
					else
						$path.= sprintf('%.2F %.2F m ', $x1, $y1);
					
					if ($coin_TR)
					{
						$xt1 = ($x2-$coin_TR[0])+$coin_TR[0]*$MyArc;
						$yt1 = ($y2+$coin_TR[1])-$coin_TR[1];
						$xt2 = ($x2-$coin_TR[0])+$coin_TR[0];
						$yt2 = ($y2+$coin_TR[1])-$coin_TR[1]*$MyArc;

						$path.= sprintf('%.2F %.2F l ', $x2-$coin_TR[0], $y2);						
						$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $x2, $y2+$coin_TR[1]);
					}
					else
						$path.= sprintf('%.2F %.2F l ', $x2, $y2);

					if ($coin_BR)
					{
						$xt1 = ($x3-$coin_BR[0])+$coin_BR[0];
						$yt1 = ($y3-$coin_BR[1])+$coin_BR[1]*$MyArc;
						$xt2 = ($x3-$coin_BR[0])+$coin_BR[0]*$MyArc;
						$yt2 = ($y3-$coin_BR[1])+$coin_BR[1];

						$path.= sprintf('%.2F %.2F l ', $x3, $y3-$coin_BR[1]);						
						$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $x3-$coin_BR[0], $y3);
					}
					else
						$path.= sprintf('%.2F %.2F l ', $x3, $y3);

					if ($coin_BL)
					{
						$xt1 = ($x4+$coin_BL[0])-$coin_BL[0]*$MyArc;
						$yt1 = ($y4-$coin_BL[1])+$coin_BL[1];
						$xt2 = ($x4+$coin_BL[0])-$coin_BL[0];
						$yt2 = ($y4-$coin_BL[1])+$coin_BL[1]*$MyArc;

						$path.= sprintf('%.2F %.2F l ', $x4+$coin_BL[0], $y4);						
						$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $x4, $y4-$coin_BL[1]);
					}
					else
						$path.= sprintf('%.2F %.2F l ', $x4, $y4);
				
					if ($coin_TL)
					{
						$xt1 = ($x1+$coin_TL[0])-$coin_TL[0];
						$yt1 = ($y1+$coin_TL[1])-$coin_TL[1]*$MyArc;
						$xt2 = ($x1+$coin_TL[0])-$coin_TL[0]*$MyArc;
						$yt2 = ($y1+$coin_TL[1])-$coin_TL[1];

						$path.= sprintf('%.2F %.2F l ', $x1, $y1+$coin_TL[1]);						
						$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $x1+$coin_TL[0], $y1);
					}
				}
				else
				{
					$path.= sprintf('%.2F %.2F m ', $x1, $y1);
					$path.= sprintf('%.2F %.2F l ', $x2, $y2);
					$path.= sprintf('%.2F %.2F l ', $x3, $y3);
					$path.= sprintf('%.2F %.2F l ', $x4, $y4);
				}

				$path.= ' h W n';
			}
			$this->_out('q '.$path.' ');			
		}
		
		public function clippingPathClose()
		{
			$this->_out(' Q');
		}
		
		public function drawCourbe($ext1_x, $ext1_y, $ext2_x, $ext2_y, $int1_x, $int1_y, $int2_x, $int2_y, $cen_x, $cen_y)
		{
			$MyArc = 4/3 * (sqrt(2) - 1);
			
			$ext1_x = $ext1_x*$this->k; $ext1_y = ($this->h-$ext1_y)*$this->k;
			$ext2_x = $ext2_x*$this->k; $ext2_y = ($this->h-$ext2_y)*$this->k;
			$int1_x = $int1_x*$this->k; $int1_y = ($this->h-$int1_y)*$this->k;
			$int2_x = $int2_x*$this->k; $int2_y = ($this->h-$int2_y)*$this->k;
			$cen_x	= $cen_x*$this->k;	$cen_y	= ($this->h-$cen_y) *$this->k;
			
			$path = '';
			
			if ($ext1_x-$cen_x!=0)
			{
				$xt1 = $cen_x+($ext1_x-$cen_x);
				$yt1 = $cen_y+($ext2_y-$cen_y)*$MyArc;
				$xt2 = $cen_x+($ext1_x-$cen_x)*$MyArc;
				$yt2 = $cen_y+($ext2_y-$cen_y);
			}
			else
			{
				$xt1 = $cen_x+($ext2_x-$cen_x)*$MyArc;
				$yt1 = $cen_y+($ext1_y-$cen_y);
				$xt2 = $cen_x+($ext2_x-$cen_x);
				$yt2 = $cen_y+($ext1_y-$cen_y)*$MyArc;

			}

			$path.= sprintf('%.2F %.2F m ', $ext1_x, $ext1_y);
			$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $ext2_x, $ext2_y);

			if ($int1_x-$cen_x!=0)
			{
				$xt1 = $cen_x+($int1_x-$cen_x)*$MyArc;
				$yt1 = $cen_y+($int2_y-$cen_y);
				$xt2 = $cen_x+($int1_x-$cen_x);
				$yt2 = $cen_y+($int2_y-$cen_y)*$MyArc;
			}
			else
			{
				$xt1 = $cen_x+($int2_x-$cen_x);
				$yt1 = $cen_y+($int1_y-$cen_y)*$MyArc;
				$xt2 = $cen_x+($int2_x-$cen_x)*$MyArc;
				$yt2 = $cen_y+($int1_y-$cen_y);

			}
			
			$path.= sprintf('%.2F %.2F l ', $int2_x, $int2_y);
			$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $int1_x, $int1_y);

			$this->_out($path . 'f');
		}
		
		public function drawCoin($ext1_x, $ext1_y, $ext2_x, $ext2_y, $int_x, $int_y, $cen_x, $cen_y)
		{
			$MyArc = 4/3 * (sqrt(2) - 1);
			
			$ext1_x = $ext1_x*$this->k; $ext1_y = ($this->h-$ext1_y)*$this->k;
			$ext2_x = $ext2_x*$this->k; $ext2_y = ($this->h-$ext2_y)*$this->k;
			$int_x  = $int_x*$this->k;  $int_y  = ($this->h-$int_y)*$this->k;
			$cen_x	= $cen_x*$this->k;	$cen_y	= ($this->h-$cen_y) *$this->k;
			
			$path = '';
			
			if ($ext1_x-$cen_x!=0)
			{
				$xt1 = $cen_x+($ext1_x-$cen_x);
				$yt1 = $cen_y+($ext2_y-$cen_y)*$MyArc;
				$xt2 = $cen_x+($ext1_x-$cen_x)*$MyArc;
				$yt2 = $cen_y+($ext2_y-$cen_y);
			}
			else
			{
				$xt1 = $cen_x+($ext2_x-$cen_x)*$MyArc;
				$yt1 = $cen_y+($ext1_y-$cen_y);
				$xt2 = $cen_x+($ext2_x-$cen_x);
				$yt2 = $cen_y+($ext1_y-$cen_y)*$MyArc;

			}

			$path.= sprintf('%.2F %.2F m ', $ext1_x, $ext1_y);
			$path.= sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $xt1, $yt1, $xt2, $yt2, $ext2_x, $ext2_y);
			$path.= sprintf('%.2F %.2F l ', $int_x, $int_y);
			$path.= sprintf('%.2F %.2F l ', $ext1_x, $ext1_y);
			
			$this->_out($path . 'f');
		}
				
		public function startTransform()
		{
			$this->_out('q');
		}
		
		public function stopTransform()
		{
			$this->_out('Q');
		}

		public function setTranslate($t_x, $t_y)
		{
			// matrice de transformation
			$tm[0]=1;
			$tm[1]=0;
			$tm[2]=0;
			$tm[3]=1;
			$tm[4]=$t_x*$this->k;
			$tm[5]=-$t_y*$this->k;
			
			$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $tm[0],$tm[1],$tm[2],$tm[3],$tm[4],$tm[5]));
		}
		
		public function setRotation($angle, $x='', $y='')
		{
			if($x === '') $x=$this->x;
			if($y === '') $y=$this->y;
			
			$y=($this->h-$y)*$this->k;
			$x*=$this->k;
			
			// matrice de transformation
			$tm[0]=cos(deg2rad($angle));
			$tm[1]=sin(deg2rad($angle));
			$tm[2]=-$tm[1];
			$tm[3]=$tm[0];
			$tm[4]=$x+$tm[1]*$y-$tm[0]*$x;
			$tm[5]=$y-$tm[0]*$y-$tm[1]*$x;
			
			$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $tm[0],$tm[1],$tm[2],$tm[3],$tm[4],$tm[5]));
		}
		
		public function SetX($x, $rtloff=false)
		{
			$this->x=$x;
		}
		
		public function SetY($y, $resetx=true, $rtloff=false)
		{
			if ($resetx)
				$this->x=$this->lMargin;
				
			$this->y=$y;
		}
		
		public function SetXY($x, $y, $rtloff=false)
		{
			$this->x=$x;
			$this->y=$y;
		}

		public function getK() { return $this->k; }
		public function getW() { return $this->w; }
		public function getH() { return $this->h; }
		public function getlMargin() { return $this->lMargin; }
		public function getrMargin() { return $this->rMargin; }
		public function gettMargin() { return $this->tMargin; }
		public function getbMargin() { return $this->bMargin; }
		public function setbMargin($v) { $this->bMargin=$v; }
		public function setcMargin($v) { $this->cMargin=$v; }
		
		public function svgSetStyle($styles)
		{
			$style = '';
			
			if ($styles['fill'])
			{
				$this->setFillColorArray($styles['fill']);
				$style.= 'F';
			}
			if ($styles['stroke'] && $styles['stroke-width'])
			{
				$this->SetDrawColorArray($styles['stroke']);
				$this->SetLineWidth($styles['stroke-width']);
				$style.= 'D';
			}
			if ($styles['fill-opacity'])
			{
				$this->SetAlpha($styles['fill-opacity']);
			}
			
			return $style;
		}
		
		public function svgRect($x, $y, $w, $h, $style)
		{
			$xa=$x; $xb=$x+$w; $xc=$x+$w; $xd=$x;
			$ya=$y; $yb=$y; $yc=$y+$h; $yd=$y+$h;
			
			if($style=='F') $op='f';
			elseif($style=='FD' || $style=='DF') $op='B';
			else $op='S';
			$this->_Point($xa, $ya, true);
			$this->_Line($xb, $yb, true);
			$this->_Line($xc, $yc, true);
			$this->_Line($xd, $yd, true);
			$this->_Line($xa, $ya, true);
			$this->_out($op);
		}

		public function svgLine($x1, $y1, $x2, $y2)
		{
			$op='S';
			$this->_Point($x1, $y1, true);
			$this->_Line($x2, $y2, true);
			$this->_out($op);
		}
		
		public function svgEllipse($x0, $y0, $rx, $ry, $style)
		{
			if($style=='F') $op='f';
			elseif($style=='FD' || $style=='DF') $op='B';
			else $op='S';
			
			$this->_Arc($x0, $y0, $rx, $ry, 0, 2*M_PI, true, true, true);
			$this->_out($op);
		}

		public function svgPolygone($actions, $style)
		{
			if($style=='F') $op='f';
			elseif($style=='FD' || $style=='DF') $op='B';
			else $op='S';

			$first = array('', 0, 0);
			$last = array(0, 0, 0, 0);
			
			foreach($actions as $action)
			{
				switch($action[0])
				{
					case 'M':
					case 'm':
						$first = $action;
						$x = $action[1]; $y = $action[2]; $xc = $x; $yc = $y;
						$this->_Point($x, $y, true);
						break;
							
					case 'Z':
					case 'z':
						$x = $first[1]; $y = $first[2]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
					break;	

					case 'L':
						$x = $action[1]; $y = $action[2]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
						break;
						
					case 'l':
						$x = $last[0]+$action[1]; $y = $last[1]+$action[2]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
						break;
						
					case 'H':
						$x = $action[1]; $y = $last[1]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
						break;	
							
					case 'h':
						$x = $last[0]+$action[1]; $y = $last[1]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
						break;	
							
					case 'V':
						$x = $last[0]; $y = $action[1]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
						break;	

					case 'v':
						$x = $last[0]; $y = $last[1]+$action[1]; $xc = $x; $yc = $y;
						$this->_Line($x, $y, true);
						break;	

					case 'A':
						$rx = $action[1];	// rx
						$ry = $action[2];	// ry
						$a = $action[3];	// angle de deviation de l'axe X
						$l = $action[4];	// large-arc-flag 
						$s = $action[5];	// sweep-flag
						$x1 = $last[0];		// begin x
						$y1 = $last[1];		// begin y
						$x2 = $action[6];	// final x
						$y2 = $action[7];	// final y
						
						$this->_Arc2($x1, $y1, $x2, $y2, $rx, $ry, $a, $l, $s, true);
						
						$x = $x2; $y = $y2; $xc = $x; $yc = $y;
						break;

					case 'a':
						$rx = $action[1];	// rx
						$ry = $action[2];	// ry
						$a = $action[3];	// angle de deviation de l'axe X
						$l = $action[4];	// large-arc-flag 
						$s = $action[5];	// sweep-flag
						$x1 = $last[0];		// begin x
						$y1 = $last[1];		// begin y
						$x2 = $last[0]+$action[6];	// final x
						$y2 = $last[1]+$action[7];	// final y
						
						$this->_Arc2($x1, $y1, $x2, $y2, $rx, $ry, $a, $l, $s, true);
						
						$x = $x2; $y = $y2; $xc = $x; $yc = $y;
						break;

					case 'C':
						$x1 = $action[1];
						$y1 = $action[2];
						$x2 = $action[3];
						$y2 = $action[4];
						$xf = $action[5];
						$yf = $action[6];
						$this->_Curve($x1, $y1, $x2, $y2,$xf, $yf, true);
						$x = $xf; $y = $yf; $xc = $x2; $yc = $y2;
						break;

					case 'c':
						$x1 = $last[0]+$action[1];
						$y1 = $last[1]+$action[2];
						$x2 = $last[0]+$action[3];
						$y2 = $last[1]+$action[4];
						$xf = $last[0]+$action[5];
						$yf = $last[1]+$action[6];
						$this->_Curve($x1, $y1, $x2, $y2,$xf, $yf, true);
						$x = $xf; $y = $yf; $xc = $x2; $yc = $y2;
						break;

					default:
						echo 'MyPDF Path : <b>'.$action[0].'</b> non reconnu...';
						exit;
				}
				$last = array($x, $y, $xc, $yc);
			}
			$this->_out($op);
		}

		protected function _Point($x, $y, $trans = false)
		{
			if ($trans) $this->ptTransform($x, $y);
			
			$this->_out(sprintf('%.2F %.2F m', $x, $y));
		}
		
		protected function _Line($x, $y, $trans = false)
		{
			if ($trans) $this->ptTransform($x, $y);

			$this->_out(sprintf('%.2F %.2F l', $x, $y));
		}
		
		protected function _Curve($x1, $y1, $x2, $y2, $x3, $y3, $trans = false)
		{
			if ($trans)
			{
				$this->ptTransform($x1, $y1);
				$this->ptTransform($x2, $y2);
				$this->ptTransform($x3, $y3);
			}
			$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1, $y1, $x2, $y2, $x3, $y3));
		}
		
		protected function _Arc($xc, $yc, $rx, $ry, $a_debut, $a_fin, $sens = true, $draw_first = true, $trans=false)
		{
			$nSeg = 8;
		
			if (!$sens) $a_debut+= M_PI*2.;
			 
			$totalAngle = $a_fin - $a_debut;
			$dt = $totalAngle/$nSeg;
			$dtm = $dt/3;
		
			$x0 = $xc; $y0 = $yc;
		
			$t1 = $a_debut;
			$a0 = $x0 + ($rx * cos($t1));
			$b0 = $y0 + ($ry * sin($t1));
			$c0 = -$rx * sin($t1);
			$d0 = $ry * cos($t1);
			if ($draw_first) $this->_Point($a0, $b0, $trans);
			for ($i = 1; $i <= $nSeg; $i++)
			{
				// Draw this bit of the total curve
				$t1 = ($i * $dt)+$a_debut;
				$a1 = $x0 + ($rx * cos($t1));
				$b1 = $y0 + ($ry * sin($t1));
				$c1 = -$rx * sin($t1);
				$d1 = $ry * cos($t1);
				$this->_Curve(
						$a0 + ($c0 * $dtm), $b0 + ($d0 * $dtm),
						$a1 - ($c1 * $dtm), $b1 - ($d1 * $dtm),
						$a1, $b1,
						$trans
					);
				$a0 = $a1;
				$b0 = $b1;
				$c0 = $c1;
				$d0 = $d1;
			}
		}
		
		protected function _Arc2($x1, $y1, $x2, $y2, $rx, $ry, $a=0, $l=0, $s=0, $trans = false)
		{
			$v = array();
			$v['x1'] = $x1;
			$v['y1'] = $y1;
			$v['x2'] = $x2;
			$v['y2'] = $y2;
			$v['rx'] = $rx;
			$v['ry'] = $ry;
			$v['xr1'] = $v['x1']*cos($a) - $v['y1']*sin($a); 
			$v['yr1'] = $v['x1']*sin($a) + $v['y1']*cos($a); 
			$v['xr2'] = $v['x2']*cos($a) - $v['y2']*sin($a); 
			$v['yr2'] = $v['x2']*sin($a) + $v['y2']*cos($a); 
			$v['Xr1'] = $v['xr1']/$v['rx']; 
			$v['Yr1'] = $v['yr1']/$v['ry']; 
			$v['Xr2'] = $v['xr2']/$v['rx']; 
			$v['Yr2'] = $v['yr2']/$v['ry']; 
			$v['dXr'] = $v['Xr2'] - $v['Xr1'];
			$v['dYr'] = $v['Yr2'] - $v['Yr1'];
			$v['D'] = $v['dXr']*$v['dXr'] + $v['dYr']*$v['dYr']; 
			
			if ($v['D']==0 || $v['D']>4)
			{
				$this->_Line($x2, $y2, $trans);
				return false;
			}
			
			$v['s1'] = array();
			$v['s2'] = array();
			$v['s1']['t'] = sqrt((4.-$v['D'])/$v['D']);
			$v['s1']['Xr'] = ($v['Xr1']+$v['Xr2'])/2. + $v['s1']['t']*($v['Yr2']-$v['Yr1'])/2.;
			$v['s1']['Yr'] = ($v['Yr1']+$v['Yr2'])/2. + $v['s1']['t']*($v['Xr1']-$v['Xr2'])/2.;
			$v['s1']['xr'] = $v['s1']['Xr']*$v['rx'];
			$v['s1']['yr'] = $v['s1']['Yr']*$v['ry'];
			$v['s1']['x'] = $v['s1']['xr']*cos($a)+$v['s1']['yr']*sin($a); 
			$v['s1']['y'] =-$v['s1']['xr']*sin($a)+$v['s1']['yr']*cos($a); 
			$v['s1']['a1'] = atan2($v['y1']-$v['s1']['y'], $v['x1']-$v['s1']['x']); 
			$v['s1']['a2'] = atan2($v['y2']-$v['s1']['y'], $v['x2']-$v['s1']['x']); 
			if ($v['s1']['a1']>$v['s1']['a2']) $v['s1']['a1']-=2*M_PI;
			
			$v['s2']['t'] = -$v['s1']['t'];
			$v['s2']['Xr'] = ($v['Xr1']+$v['Xr2'])/2. + $v['s2']['t']*($v['Yr2']-$v['Yr1'])/2.;
			$v['s2']['Yr'] = ($v['Yr1']+$v['Yr2'])/2. + $v['s2']['t']*($v['Xr1']-$v['Xr2'])/2.;
			$v['s2']['xr'] = $v['s2']['Xr']*$v['rx']; 
			$v['s2']['yr'] = $v['s2']['Yr']*$v['ry']; 
			$v['s2']['x'] = $v['s2']['xr']*cos($a)+$v['s2']['yr']*sin($a); 
			$v['s2']['y'] =-$v['s2']['xr']*sin($a)+$v['s2']['yr']*cos($a); 
			$v['s2']['a1'] = atan2($v['y1']-$v['s2']['y'], $v['x1']-$v['s2']['x']); 
			$v['s2']['a2'] = atan2($v['y2']-$v['s2']['y'], $v['x2']-$v['s2']['x']); 
			if ($v['s2']['a1']>$v['s2']['a2']) $v['s2']['a1']-=2*M_PI;
			
			if (!$l)
			{
				if ($s)
				{
					$xc = $v['s2']['x'];
					$yc = $v['s2']['y'];
					$a1 = $v['s2']['a1'];
					$a2 = $v['s2']['a2'];
					$this->_Arc($xc, $yc, $rx, $ry, $a1, $a2, true, false, $trans);
					
				}
				else
				{
					$xc = $v['s1']['x'];
					$yc = $v['s1']['y'];
					$a1 = $v['s1']['a1'];
					$a2 = $v['s1']['a2'];
					$this->_Arc($xc, $yc, $rx, $ry, $a1, $a2, false, false, $trans);
				}
			}
			else
			{
				if ($s)
				{
					$xc = $v['s1']['x'];
					$yc = $v['s1']['y'];
					$a1 = $v['s1']['a1'];
					$a2 = $v['s1']['a2'];
					$this->_Arc($xc, $yc, $rx, $ry, $a1, $a2, true, false, $trans);
				}
				else
				{
					$xc = $v['s2']['x'];
					$yc = $v['s2']['y'];
					$a1 = $v['s2']['a1'];
					$a2 = $v['s2']['a2'];
					$this->_Arc($xc, $yc, $rx, $ry, $a1, $a2, false, false, $trans);
				}
			}
		}
		
		public function ptTransform(&$x,  &$y, $trans=true)
		{
			$nb = count($this->transf);
			if ($nb)	$m = $this->transf[$nb-1];
			else		$m = array(1,0,0,1,0,0);
			
			list($x,$y) = array(($x*$m[0]+$y*$m[2]+$m[4]),($x*$m[1]+$y*$m[3]+$m[5]));
			
			if ($trans)
			{
				$x = $x*$this->k;
				$y = ($this->h-$y)*$this->k;
			}
			
			return true;
		}
	
		public function doTransform($n = null)
		{
			$nb = count($this->transf);
			if ($nb)	$m = $this->transf[$nb-1];
			else		$m = array(1,0,0,1,0,0);
			
			if (!$n) $n = array(1,0,0,1,0,0);

			$n = array(
					$m[0]*$n[0]+$m[2]*$n[1],
					$m[1]*$n[0]+$m[3]*$n[1],
					$m[0]*$n[2]+$m[2]*$n[3],
					$m[1]*$n[2]+$m[3]*$n[3],
					$m[0]*$n[4]+$m[2]*$n[5]+$m[4],  
					$m[1]*$n[4]+$m[3]*$n[5]+$m[5]  
				);	
				
//			echo 'do-'.count($this->transf).' => '.print_r($n, true).'<br>';
			$this->transf[] = $n;
		}
		
		public function undoTransform()
		{
			array_pop($this->transf);
//			echo 'un-'.count($this->transf).'<br>';
		}
		
		public function myBarcode($code, $type, $x, $y, $w, $h, $fontsize_label=false, $color)
		{
			$fontsize = $this->FontSizePt;
			
			$style = array(
				'position' => 'S',
				'text' => $fontsize_label ? true : false,
				'fgcolor' => $color,
    			'bgcolor' => false,
			);

			$this->write1DBarcode($code, $type, $x, $y, $w, $h, '', $style, 'N');
			
			if ($fontsize_label)
			{
				$h+= ($fontsize_label);	
			}
			
			$code_w = $w;
			$code_h = $h;
			
			return array($code_w, $code_h);
		}

		public function createIndex(&$obj, $titre = 'Index', $size_title = 20, $size_bookmark = 15, $bookmark_title = true, $display_page = true, $page = null, $font_name = 'helvetica')
		{
			if ($bookmark_title) $this->Bookmark($titre, 0, -1);
			
			//Index title
			$this->SetFont($font_name, '', $size_title);
			$this->Cell(0,5,$titre,0,1,'C');
			$this->SetFont($font_name, '', $size_bookmark);
			$this->Ln(10);
			
			$size=sizeof($this->outlines);
			$PageCellSize=$this->GetStringWidth('p. '.$this->outlines[$size-1]['p'])+2;
			for ($i=0;$i<$size;$i++)
			{
				if ($this->getY()+$this->FontSize>=($this->h - $this->bMargin))
				{
					$obj->INDEX_NewPage($page);
					$this->SetFont($font_name, '', $size_bookmark);
				}
				
				//Offset
				$level=$this->outlines[$i]['l'];
				if($level>0) $this->Cell($level*8);
				
				//Caption
				$str=$this->outlines[$i]['t'];
				$strsize=$this->GetStringWidth($str);
				$avail_size=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-4;
				while ($strsize>=$avail_size)
				{
					$str=substr($str,0,-1);
					$strsize=$this->GetStringWidth($str);
				}
				if ($display_page)
				{
					$this->Cell($strsize+2,$this->FontSize+2,$str);
				
					//Filling dots
					$w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+2);
					$nb=$w/$this->GetStringWidth('.');
					$dots=str_repeat('.',$nb);
					$this->Cell($w,$this->FontSize+2,$dots,0,0,'R');

					//Page number
					$this->Cell($PageCellSize,$this->FontSize+2,'p. '.$this->outlines[$i]['p'],0,1,'R');
				}
				else
				{
					$this->Cell($strsize+2,$this->FontSize+2,$str, 0, 1);					
				}
			}
		}
	}
}
